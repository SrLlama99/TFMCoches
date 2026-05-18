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
use App\Entity\FotoGaraje;
use App\Entity\cocheGaraje;
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

            if (!$model) {
                throw $this->createNotFoundException('Modelo no encontrado');
            }

            // Check that the id of the model is from the brand of the link
            if ($marca && method_exists($marca, 'getIdMarca')) {
                $marcaId = $marca->getIdMarca();
            } else {
                $marcaId = null;
            }

            if ($marcaId === null || $model->getMarca() !== $marcaId) {
                throw $this->createNotFoundException('El modelo no pertenece a la marca indicada');
            }

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

            // Load the Valoraciones from the database
            $valorRepo = $em->getRepository(Valoracion::class);
            $usersRepo = $em->getRepository(Users::class);

            // Get the last 10 Valoraciones to the model's cars
            $qbInit = $valorRepo->createQueryBuilder('v')
                ->join('v.idCoche', 'c')
                ->where('c.modelo = :modelo')
                ->setParameter('modelo', $model)
                ->orderBy('v.fecha', 'DESC')
                ->setMaxResults(10);

            $valoracionesEntities = $qbInit->getQuery()->getResult();

            $valoraciones = array_map(function ($v) use ($usersRepo) {
                $username = 'Usuario';
                // obtain username if exists
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
                    'motor' => $motorName,
                    'color' => $color,
                    'anio' => $anio,
                    'transmision' => $transmision,
                ];
            }, $valoracionesEntities);

            // get all the engines to use in the select of engines
            $allMotors = $engineRepo->findAll();

            return $this->render('model/model.html.twig', [
                'model' => $model,
                'marca' => $marca,
                'cars' => $cars,
                'listaMotores' => $nombresMotores,
                'motores' => $allMotors,
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
        // Require user to put a Valoracion 
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
            // Search engine
            $motorRepo = $em->getRepository(Motor::class);
            $motor = $motorRepo->findOneBy(['nombreMotor' => $motorName]);

            // Search a car with the same specifications
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
                // New car if not exists
                $coche = new Coche();
                $coche->setModelo($model);
                $coche->setMotor($motor);
                $coche->setCocheColor($color);
                $coche->setCocheAnio($anio);
                $coche->setCocheTransmision($transmision);
                $em->persist($coche);
                $em->flush();
            }

            $valoracion = new Valoracion();
            $valoracion->setIdUsuario(method_exists($user, 'getUserId') ? $user->getUserId() : (method_exists($user, 'getId') ? $user->getId() : null));
            $valoracion->setIdCoche($coche);
            $valoracion->setEstrellas($puntuacion);
            $valoracion->setComentario($comentario ?: '');
            $valoracion->setFecha((new \DateTime())->format('Y-m-d H:i:s'));
            $em->persist($valoracion);
            $em->flush();

            if ($anadirGaraje === 1) {
                $garaje = new \App\Entity\cocheGaraje();
                $garaje->setUsuario(method_exists($user, 'getUserId') ? $user->getUserId() : (method_exists($user, 'getId') ? $user->getId() : null));
                $garaje->setCoche($coche->getcocheId());
                $garaje->setNotas($notasPropietario ?: '');

                // Process uploaded photos (if any) and collect filenames to store in DB
                $photoNames = [];
                $movedFiles = [];
                if (!empty($_FILES["photos"]["name"])) {
                    $allowed = ["png", "jpg", "jpeg", "webp"];

                    // Use absolute path to public folder
                    $uploadDir = $this->getParameter('kernel.project_dir') . '/public/assets/images/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    foreach ($_FILES["photos"]["name"] as $key => $val) {
                        $originalName = $_FILES["photos"]["name"][$key];
                        $tmpName      = $_FILES["photos"]["tmp_name"][$key];
                        $extension    = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                        if (in_array($extension, $allowed)) {
                            $newName = (method_exists($user, 'getUserId') ? $user->getUserId() : (method_exists($user, 'getId') ? $user->getId() : 'user')) . "_" . time() . "_" . $key . "." . $extension;
                            $targetPath = $uploadDir . $newName;

                            if (is_uploaded_file($tmpName) && move_uploaded_file($tmpName, $targetPath)) {
                                $photoNames[] = $newName; // filename
                                $movedFiles[] = $targetPath; // absolute path
                            }
                        }
                    }
                }

                // Persist garaje and create FotoGaraje rows for each uploaded photo
                $em->persist($garaje);

                if (!empty($photoNames)) {
                    foreach ($photoNames as $fname) {
                        $foto = new FotoGaraje();
                        $foto->setPoseedor($user);
                        $foto->setCoche($coche->getcocheId());
                        // store web-accessible path
                        $foto->setUrl('/assets/images/' . $fname);
                        $em->persist($foto);
                    }
                }

                $em->flush();
            }
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }

        // Prepare response to send to the DOM
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

    #[Route('/profile/{name}', name: 'profile')]
    public function profile(EntityManagerInterface $em, string $name): Response
    {
        $usersRepo = $em->getRepository(Users::class);
        $carsGarageRepo = $em->getRepository(cocheGaraje::class);
        $carsRepo = $em->getRepository(Coche::class);
        $modelRepo = $em->getRepository(Modelo::class);
        $marcaRepo = $em->getRepository(Marca::class);
        $valorRepo = $em->getRepository(Valoracion::class);

        $usuario = $usersRepo->findOneBy(['UserName' => strtolower($name)]);
        if (!$usuario) {
            throw $this->createNotFoundException('Usuario no encontrado');
        }

        // Handle possible inconsistency: in some places the Users entity is stored,
        // elsewhere the user id is stored in `cocheGaraje.usuario`.
        $usuarioId = null;
        if (method_exists($usuario, 'getUserId')) {
            $usuarioId = $usuario->getUserId();
        } elseif (method_exists($usuario, 'getId')) {
            $usuarioId = $usuario->getId();
        }

        // `cocheGaraje` does not define associations (stores IDs). We retrieve records
        // using the user id and then resolve entities manually.
        if ($usuarioId === null) {
            throw $this->createNotFoundException('Usuario inválido');
        }

        $registrosGaraje = $carsGarageRepo->findBy(['usuario' => $usuarioId]);

        $cochesDelUsuario = [];

        foreach ($registrosGaraje as $registro) {
            // `getCoche()` may return the Coche entity or an id depending on how it was persisted.
            $coche = $registro->getCoche();

            if (is_int($coche) || is_string($coche)) {
                $coche = $carsRepo->find((int) $coche);
            }

            if (!$coche) {
                continue;
            }

            // Get model: it may be an entity or an id
            $modelo = null;
            if (method_exists($coche, 'getModelo')) {
                $modelo = $coche->getModelo();
                if ($modelo && !is_object($modelo)) {
                    $modelo = $modelRepo->find($modelo);
                }
            }

            // Get brand from the model: it may be an entity or an id
            $marca = null;
            if ($modelo && method_exists($modelo, 'getMarca')) {
                $marcaVal = $modelo->getMarca();
                if (is_object($marcaVal)) {
                    $marca = $marcaVal;
                } elseif ($marcaVal) {
                    $marca = $marcaRepo->find($marcaVal);
                }
            }

            // get the rating this user left for this car (if any)
            $valoracionUsuario = null;
            if ($usuarioId !== null) {
                $v = $valorRepo->findOneBy(['idUsuario' => $usuarioId, 'idCoche' => $coche]);
                if ($v) {
                    $valoracionUsuario = [
                        'estrellas' => $v->getEstrellas(),
                        'comentario' => $v->getComentario(),
                        'fecha' => $v->getFecha(),
                    ];
                }
            }

            $cochesDelUsuario[] = [
                'garaje' => $registro,
                'coche' => $coche,
                'modelo' => $modelo,
                'marca' => $marca,
                'valoracion' => $valoracionUsuario,
            ];
        }

        // --- Most-rated cars by this user ---
        $cochesMasValorados = [];
        $seen = [];

        $valoracionesUsuario = $valorRepo->createQueryBuilder('v')
            ->where('v.idUsuario = :uid')
            ->setParameter('uid', $usuarioId)
            ->orderBy('v.estrellas', 'DESC')
            ->addOrderBy('v.fecha', 'DESC')
            ->getQuery()
            ->getResult();

        foreach ($valoracionesUsuario as $v) {

            $vcoche = $v->getIdCoche();
            if (is_int($vcoche) || is_string($vcoche)) {
                $vcoche = $carsRepo->find((int) $vcoche);
            }
            if (!$vcoche) continue;

            $cid = method_exists($vcoche, 'getcocheId') ? $vcoche->getcocheId() : (method_exists($vcoche, 'getId') ? $vcoche->getId() : null);
            if ($cid === null) continue;
            if (in_array($cid, $seen, true)) continue;
            $seen[] = $cid;

            $vmodelo = null;
            if (method_exists($vcoche, 'getModelo')) {
                $vmodelo = $vcoche->getModelo();
                if ($vmodelo && !is_object($vmodelo)) {
                    $vmodelo = $modelRepo->find($vmodelo);
                }
            }

            $vmarca = null;
            if ($vmodelo && method_exists($vmodelo, 'getMarca')) {
                $marcaVal = $vmodelo->getMarca();
                if (is_object($marcaVal)) {
                    $vmarca = $marcaVal;
                } elseif ($marcaVal) {
                    $vmarca = $marcaRepo->find($marcaVal);
                }
            }

            $cochesMasValorados[] = [
                'coche' => $vcoche,
                'modelo' => $vmodelo,
                'marca' => $vmarca,
                'valoracion' => [
                    'estrellas' => $v->getEstrellas(),
                    'comentario' => $v->getComentario(),
                    'fecha' => $v->getFecha(),
                ],
            ];
        }

        // --- Latest ratings by the user (most recent) ---
        $ultimasValoraciones = [];
        $recentVals = $valorRepo->createQueryBuilder('rv')
            ->where('rv.idUsuario = :uid')
            ->setParameter('uid', $usuarioId)
            ->orderBy('rv.fecha', 'DESC')
            ->getQuery()
            ->getResult();

        foreach ($recentVals as $v) {
            $vcoche = $v->getIdCoche();
            if (is_int($vcoche) || is_string($vcoche)) {
                $vcoche = $carsRepo->find((int) $vcoche);
            }
            if (!$vcoche) continue;

            $vmodelo = null;
            if (method_exists($vcoche, 'getModelo')) {
                $vmodelo = $vcoche->getModelo();
                if ($vmodelo && !is_object($vmodelo)) {
                    $vmodelo = $modelRepo->find($vmodelo);
                }
            }

            $vmarca = null;
            if ($vmodelo && method_exists($vmodelo, 'getMarca')) {
                $marcaVal = $vmodelo->getMarca();
                if (is_object($marcaVal)) {
                    $vmarca = $marcaVal;
                } elseif ($marcaVal) {
                    $vmarca = $marcaRepo->find($marcaVal);
                }
            }

            $ultimasValoraciones[] = [
                'coche' => $vcoche,
                'modelo' => $vmodelo,
                'marca' => $vmarca,
                'valoracion' => [
                    'estrellas' => $v->getEstrellas(),
                    'comentario' => $v->getComentario(),
                    'fecha' => $v->getFecha(),
                ],
            ];
        }

        return $this->render('profile/profile.html.twig', [
            'usuario' => $usuario,
            'coches' => $cochesDelUsuario,
            'valorados' => $cochesMasValorados,
            'ultimos' => $ultimasValoraciones
        ]);
    }
}
