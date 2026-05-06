<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Coche;
use App\Entity\Modelo;
use App\Entity\Marca;
use App\Entity\Motor;
use App\Entity\Valoracion;
use App\Entity\Users;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ProfilesController extends AbstractController
{
    #[Route('/marca/{name}/{id}', name: 'marca')]
    public function home(EntityManagerInterface $em, string $name, ?int $id = null): Response
    {
        // Define repositories to use
        $carsRepo = $em->getRepository(Coche::class);
        $modelRepo = $em->getRepository(Modelo::class);
        $marcaRepo = $em->getRepository(Marca::class);
        $engineRepo = $em->getRepository(Motor::class);

        $marca = $marcaRepo->findOneBy(['nombreMarca' => strtoupper($name)]);

        if ($id) {
            $model = $modelRepo->findOneBy(['modeloId' => $id]);
            $cars = $carsRepo->findBy(['modelo' => $model]);

            $nombresMotores = array_unique(array_map(function ($coche) {
                return $coche->getMotor()->getNombreMotor();
            }, $cars));

            $coloresUnicos = array_unique(array_map(function ($coche) {
                return $coche->getCocheColor();
            }, $cars));

            $aniosUnicos = array_unique(array_map(function ($coche) {
                return $coche->getCocheAnio();
            }, $cars));

            $transmisionesUnicas = array_unique(array_map(function ($coche) {
                return $coche->getCocheTransmision();
            }, $cars));

            // Cargar las valoraciones (comentarios) desde la base de datos
            $valorRepo = $em->getRepository(Valoracion::class);
            $usersRepo = $em->getRepository(Users::class);

            // Obtener las 10 valoraciones más recientes para este modelo (todos sus coches)
            $qbInit = $valorRepo->createQueryBuilder('v')
                ->join('v.idCoche', 'c')
                ->where('c.modelo = :modelo')
                ->setParameter('modelo', $model)
                ->orderBy('v.fecha', 'DESC')
                ->setMaxResults(10);

            $valoracionesEntities = $qbInit->getQuery()->getResult();

            $valoraciones = array_map(function ($v) use ($usersRepo) {
                $username = 'Usuario';
                // obtener nombre de usuario si existe
                if (method_exists($v, 'getIdUsuario')) {
                    $user = $usersRepo->find($v->getIdUsuario());
                    if ($user) {
                        $username = $user->getUserName();
                    }
                }

                    $coche = null;
                    $motorName = null;
                    $color = null;
                    $anio = null;
                    $transmision = null;

                    if (method_exists($v, 'getIdCoche')) {
                        $coche = $v->getIdCoche();
                        if ($coche) {
                            $motorName = method_exists($coche, 'getMotor') && $coche->getMotor() ? $coche->getMotor()->getNombreMotor() : null;
                            $color = method_exists($coche, 'getCocheColor') ? $coche->getCocheColor() : null;
                            $anio = method_exists($coche, 'getCocheAnio') ? $coche->getCocheAnio() : null;
                            $transmision = method_exists($coche, 'getCocheTransmision') ? $coche->getCocheTransmision() : null;
                        }
                    }

                    return [
                        'username' => $username,
                        'comentario' => $v->getComentario(),
                        'estrellas' => $v->getEstrellas(),
                        'fecha' => $v->getFecha(),
                        'timeAgo' => $this->formatTimeAgo($v->getFecha()),
                        'motor' => $motorName,
                        'color' => $color,
                        'anio' => $anio,
                        'transmision' => $transmision,
                    ];
                }, $valoracionesEntities);

            return $this->render('model/model.html.twig', [
                'model' => $model,
                'marca' => $marca,
                'cars' => $cars,
                'listaMotores' => $nombresMotores,
                'listaColores' => $coloresUnicos,
                'listaAnios' => $aniosUnicos,
                'listaTransmisiones' => $transmisionesUnicas,
                'valoraciones' => $valoraciones,
            ]);
        }

        // Get data for the page

        $model = $modelRepo->findBy(['marca' => $marca]);

        return $this->render('marca/marca.html.twig', [
            'marca' => $marca,
            'modelos' => $model,
        ]);
    }

    /**
     * Devuelve una cadena relativa en español: "hace X minutos".
     */
    private function formatTimeAgo($fecha): string
    {
        try {
            $dt = $fecha instanceof \DateTimeInterface ? $fecha : new \DateTime((string) $fecha);
        } catch (\Exception $e) {
            return 'right now';
        }

        $now = new \DateTime();
        $diff = $now->getTimestamp() - $dt->getTimestamp();
        if ($diff < 60) {
            $s = $diff;
            return $s . ' seconds ago';
        }

        $min = (int) floor($diff / 60);
        if ($min < 60) {
            return $min . ' minutes ago';
        }

        $hours = (int) floor($diff / 3600);
        if ($hours < 24) {
            return $hours . ' hours ago';
        }

        $days = (int) floor($diff / 86400);
        if ($days < 7) {
            return $days . ' days ago';
        }

        $weeks = (int) floor($days / 7);
        if ($weeks < 5) {
            return $weeks . ' weeks ago';
        }

        $months = (int) floor($days / 30);
        if ($months < 12) {
            return $months . ' months ago';
        }

        $years = (int) floor($days / 365);
        return $years . ' years ago';
    }

    #[Route('/modelo/{modelId}/comments', name: 'model_comments')]
    public function comments(EntityManagerInterface $em, int $modelId, Request $request): JsonResponse
    {
        $offset = max(0, (int) $request->query->get('offset', 0));
        $limit = max(1, (int) $request->query->get('limit', 10));

        $modelRepo = $em->getRepository(Modelo::class);
        $model = $modelRepo->find($modelId);
        if (!$model) {
            return new JsonResponse(['error' => 'Modelo no encontrado'], 404);
        }

        $valorRepo = $em->getRepository(Valoracion::class);

        $qb = $valorRepo->createQueryBuilder('v')
            ->join('v.idCoche', 'c')
            ->where('c.modelo = :modelo')
            ->setParameter('modelo', $model)
            ->orderBy('v.fecha', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $results = $qb->getQuery()->getResult();

        $usersRepo = $em->getRepository(Users::class);

        $data = array_map(function ($v) use ($usersRepo) {
            $username = 'Usuario';
            if (method_exists($v, 'getIdUsuario')) {
                $user = $usersRepo->find($v->getIdUsuario());
                if ($user) {
                    $username = $user->getUserName();
                }
            }

            $coche = null;
            $motorName = null;
            $color = null;
            $anio = null;
            $transmision = null;

            if (method_exists($v, 'getIdCoche')) {
                $coche = $v->getIdCoche();
                if ($coche) {
                    $motorName = method_exists($coche, 'getMotor') && $coche->getMotor() ? $coche->getMotor()->getNombreMotor() : null;
                    $color = method_exists($coche, 'getCocheColor') ? $coche->getCocheColor() : null;
                    $anio = method_exists($coche, 'getCocheAnio') ? $coche->getCocheAnio() : null;
                    $transmision = method_exists($coche, 'getCocheTransmision') ? $coche->getCocheTransmision() : null;
                }
            }

            return [
                'username' => $username,
                'comentario' => $v->getComentario(),
                'estrellas' => $v->getEstrellas(),
                'fecha' => $v->getFecha(),
                'timeAgo' => $this->formatTimeAgo($v->getFecha()),
                'motor' => $motorName,
                'color' => $color,
                'anio' => $anio,
                'transmision' => $transmision,
            ];
        }, $results);

        return new JsonResponse(['data' => $data, 'count' => count($data)]);
    }

    #[Route('/modelo/{modelId}/add-rating', name: 'model_add_rating', methods: ['POST'])]
    public function addRating(EntityManagerInterface $em, int $modelId, Request $request): JsonResponse
    {
        // Usuario autenticado
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $modelRepo = $em->getRepository(Modelo::class);
        $model = $modelRepo->find($modelId);
        if (!$model) {
            return new JsonResponse(['error' => 'Modelo no encontrado'], 404);
        }

        $motorName = trim($request->request->get('motor', ''));
        $transmision = trim($request->request->get('transmision', ''));
        $color = trim($request->request->get('color', ''));
        $anio = (int) $request->request->get('anio', 0);
        $puntuacion = (int) $request->request->get('puntuacion', 0);
        $comentario = trim($request->request->get('opinion', ''));
        $anadirGaraje = (int) $request->request->get('anadir_garaje', 0);
        $notasPropietario = trim($request->request->get('notas_propietario', ''));

        try {
            // Buscar o crear Motor
            $motorRepo = $em->getRepository(Motor::class);
            $motor = $motorRepo->findOneBy(['nombreMotor' => $motorName]);
            if (!$motor) {
                $motor = new Motor();
                $motor->setNombreMotor($motorName);
                $motor->setCarburante(0);
                $em->persist($motor);
                $em->flush();
            }

            // Buscar coche con las mismas especificaciones
            $cocheRepo = $em->getRepository(Coche::class);
            $qb = $cocheRepo->createQueryBuilder('c')
                ->where('c.modelo = :modelo')
                ->andWhere('c.motor = :motor')
                ->andWhere('c.cocheColor = :color')
                ->andWhere('c.cocheAnio = :anio')
                ->andWhere('c.cocheTransmision = :transmision')
                ->setParameter('modelo', $model)
                ->setParameter('motor', $motor)
                ->setParameter('color', $color)
                ->setParameter('anio', $anio)
                ->setParameter('transmision', $transmision)
                ->setMaxResults(1);

            $coche = $qb->getQuery()->getOneOrNullResult();

            if (!$coche) {
                // Crear nuevo coche
                $coche = new Coche();
                $coche->setModelo($model);
                $coche->setMotor($motor);
                $coche->setCocheColor($color);
                $coche->setCocheAnio($anio);
                $coche->setCocheTransmision($transmision);
                $em->persist($coche);
                $em->flush();
            }

            // Crear la valoración
            $valoracion = new Valoracion();
            $valoracion->setIdUsuario(method_exists($user, 'getUserId') ? $user->getUserId() : (method_exists($user, 'getId') ? $user->getId() : null));
            $valoracion->setIdCoche($coche);
            $valoracion->setEstrellas($puntuacion);
            $valoracion->setComentario($comentario ?: '');
            $valoracion->setFecha((new \DateTime())->format('Y-m-d H:i:s'));
            $em->persist($valoracion);
            $em->flush();

            // Si se añadió al garaje, crear entrada en cocheGaraje
            if ($anadirGaraje === 1) {
                $garaje = new \App\Entity\cocheGaraje();
                $garaje->setUsuario(method_exists($user, 'getUserId') ? $user->getUserId() : (method_exists($user, 'getId') ? $user->getId() : null));
                $garaje->setCoche($coche->getcocheId());
                $garaje->setNotas($notasPropietario ?: '');
                $em->persist($garaje);
                $em->flush();
            }
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }

        // Preparar respuesta con la nueva valoración para añadir al DOM
        $response = [
            'success' => true,
            'data' => [
                'username' => method_exists($user, 'getUserName') ? $user->getUserName() : 'Usuario',
                'comentario' => $comentario,
                'estrellas' => $puntuacion,
                'timeAgo' => 'just now',
                'motor' => $motorName,
                'color' => $color,
                'anio' => $anio,
                'transmision' => $transmision,
            ],
        ];

        return new JsonResponse($response);
    }
}
