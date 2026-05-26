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
use Symfony\Component\HttpFoundation\JsonResponse;

final class CarsController extends AbstractController
{
#[Route('/marca/{name}/{id}', name: 'marca', requirements: ['id' => '\\d+'])]
    public function home(EntityManagerInterface $em, string $name, ?int $id = null): Response
    {
        // Define repositories to use
        $carsRepo = $em->getRepository(Coche::class);
        $modelRepo = $em->getRepository(Modelo::class);
        $marcaRepo = $em->getRepository(Marca::class);
        $engineRepo = $em->getRepository(Motor::class);

        $marca = $marcaRepo->findOneBy(['nombreMarca' => strtoupper($name)]);
        if($marca == null){
            return $this->redirectToRoute('home');
        }

        if ($id) {
            $model = $modelRepo->findOneBy(['modeloId' => $id]);

            if (!$model) {
                throw $this->createNotFoundException('Modelo no encontrado');
            }

            // Check that the id of the model is from the brand of the link
            $marcaId = $marca?->getIdMarca() ?? null;

            if ($marcaId === null || $model->getMarca()->getIdMarca() !== $marcaId) {
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
                $profilePic = '';
                // obtain username if exists
                $userId = $v?->getIdUsuario() ?? null;
                if ($userId) {
                    $user = $usersRepo->find($userId);
                    if ($user) {
                        $username = $user->getUserName();
                        $profilePic = $user->getProfilePic();
                    }
                }

                $coche = null;
                $motorName = null;
                $color = null;
                $anio = null;
                $transmision = null;

                $coche = $v?->getIdCoche() ?? null;
                if (is_object($coche)) {
                    $motorName = $coche->getMotor()?->getNombreMotor() ?? null;
                    $color = $coche->getCocheColor() ?? null;
                    $anio = $coche->getCocheAnio() ?? null;
                    $transmision = $coche->getCocheTransmision() ?? null;
                }

                return [
                    'id' => $v->getIdValoracion(),
                    'username' => $username,
                    'profilePic' => $profilePic,
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
            $valoracion->setIdUsuario($user?->getUserId());
            $valoracion->setIdCoche($coche);
            $valoracion->setEstrellas($puntuacion);
            $valoracion->setComentario($comentario ?: '');
            $valoracion->setFecha((new \DateTime())->format('Y-m-d H:i:s'));
            $em->persist($valoracion);
            $em->flush();

            if ($anadirGaraje === 1) {
                $garaje = new \App\Entity\cocheGaraje();
                $garaje->setUsuario($user?->getUserId());
                $garaje->setCoche($coche->getcocheId());
                $garaje->setNotas($notasPropietario ?: '');

                // Process uploaded photos (if any) and collect filenames to store in DB
                $photoNames = [];
                // Use Symfony's UploadedFile handling instead of raw $_FILES keys
                $uploadedFiles = $request->files->get('garaje_image');
                if (!empty($uploadedFiles) && is_array($uploadedFiles)) {
                    $allowed = ["png", "jpg", "jpeg", "webp"];

                    // Use absolute path to public folder
                    $uploadDir = $this->getParameter('kernel.project_dir') . '/public/assets/images/cars/';


                    foreach ($uploadedFiles as $key => $file) {
                        if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                            $extension = strtolower($file->getClientOriginalExtension());

                            if (in_array($extension, $allowed)) {
                                $userId = $user?->getUserId();
                                $cocheId = is_object($coche) ? ($coche->getCocheId() ?? $coche->getId() ?? 'coche') : ($coche ?? 'coche');
                                $timestamp = time();
                                $newName = sprintf('%s_%s_%s_%s.%s', $userId, $cocheId, $timestamp, $key, $extension);
                                try {
                                    $targetPath = $uploadDir . $newName;
                                    // If file already exists, skip moving to avoid duplicates
                                    if (!file_exists($targetPath)) {
                                        $file->move($uploadDir, $newName);
                                    }
                                    $photoNames[] = $newName;
                                } catch (\Exception $e) {
                                    
                                }
                            }
                        }
                    }
                }

                // Persist garaje and create FotoGaraje rows for each uploaded photo
                $em->persist($garaje);
                $em->flush();

                if (!empty($photoNames)) {
                    foreach ($photoNames as $fname) {
                        $foto = new FotoGaraje();
                        $foto->setPoseedor($user);
                        $foto->setCoche($coche->getcocheId());
                        $foto->setUrl($fname);
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
                'id' => $valoracion->getIdValoracion(),
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
            $userId = $v?->getIdUsuario() ?? null;
            if ($userId) {
                $user = $usersRepo->find($userId);
                if ($user) {
                    $username = $user->getUserName();
                }
            }

            $coche = $v?->getIdCoche() ?? null;
            $motorName = null;
            $color = null;
            $anio = null;
            $transmision = null;

            if (is_object($coche)) {
                $motorName = $coche->getMotor()?->getNombreMotor() ?? null;
                $color = $coche->getCocheColor() ?? null;
                $anio = $coche->getCocheAnio() ?? null;
                $transmision = $coche->getCocheTransmision() ?? null;
            }

            return [
                'id' => $v->getIdValoracion(),
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

    #[Route('/marca/{id}/update', name: 'marca_update', methods: ['POST'])]
    public function updateMarca(EntityManagerInterface $em, Request $request, int $id): Response
    {
        if (! $this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['success' => false, 'error' => 'Forbidden'], 403);
        }

        $repo = $em->getRepository(Marca::class);
        $marca = $repo->find($id);
        if (! $marca) {
            return new JsonResponse(['success' => false, 'error' => 'Not found'], 404);
        }

        $nombre = strtoupper(trim((string) $request->request->get('nombre', '')));
        $url = trim((string) $request->request->get('url', ''));

        if ($nombre !== '') $marca->setnombreMarca($nombre);
        if ($url !== '') $marca->seturlMarca($url);

        $uploaded = $request->files->get('logo');
        if ($uploaded && $uploaded->isValid()) {
            try {
                $extension = strtolower($uploaded->getClientOriginalExtension());
                $allowed = ['png','jpg','jpeg','webp'];
                if (in_array($extension, $allowed, true)) {
                    $uploadDir = $this->getParameter('kernel.project_dir') . '/public/assets/images/brands/';
                    $newName = sprintf('brand_%s_%s.%s', $id, time(), $extension);
                    $uploaded->move($uploadDir, $newName);
                    $marca->seturlLogo($newName);
                }
            } catch (\Exception $e) {
                
            }
        }

        try {
            $em->persist($marca);
            $em->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }

        // Build redirect URL to the brand page with the (possibly) new name
        $redirectUrl = $this->generateUrl('marca', [
            'name' => $marca->getnombreMarca(),
        ]);

        // If the request is AJAX, return JSON including the redirect URL
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['success' => true, 'data' => [
                'nombre' => $marca->getnombreMarca(),
                'url' => $marca->geturlMarca(),
                'logo' => $marca->geturlLogo(),
            ], 'redirect' => $redirectUrl]);
        }

        // Otherwise perform a normal redirect to the new brand URL
        return $this->redirect($redirectUrl);
    }

    #[Route('/deleteMarca/{id}', name: 'app_borrarMarca')]
    public function deleteMarca(EntityManagerInterface $em, int $id)
    {
        $marcaRepo = $em->getRepository(Marca::class);
        $modeloRepo = $em->getRepository(Modelo::class);
        $cocheRepo = $em->getRepository(Coche::class);
        $valorRepo = $em->getRepository(Valoracion::class);
        $marca = $marcaRepo->find($id);
        if ($marca) {
            $modelos = $modeloRepo->findBy(['marca' => $marca->getIdMarca()]);
            foreach ($modelos as $modelo) {
                $coches = $cocheRepo->findBy(['modelo' => $modelo]);
                foreach ($coches as $coche) {
                    $valoraciones = $valorRepo->findBy(['idCoche' => $coche]);
                    foreach ($valoraciones as $v) {
                        $em->remove($v);
                    }
                    $em->remove($coche);
                }
                $em->remove($modelo);
            }
            $em->remove($marca);
            $em->flush();
        }
        return $this->redirectToRoute('home');
    }
    
    #[Route('/deleteModelo/{id}', name: 'app_borrarModelo')]
    public function deleteModelo(EntityManagerInterface $em, int $id)
    {
        $modeloRepo = $em->getRepository(Modelo::class);
        $cocheRepo = $em->getRepository(Coche::class);
        $valorRepo = $em->getRepository(Valoracion::class);

        $modelo = $modeloRepo->find($id);

        if ($modelo) {

            // coches del modelo
            $coches = $cocheRepo->findBy(['modelo' => $modelo]);

            foreach ($coches as $coche) {

                // valoraciones del coche
                $valoraciones = $valorRepo->findBy(['idCoche' => $coche]);

                foreach ($valoraciones as $v) {
                    $em->remove($v);
                }

                $em->remove($coche);
            }

            $em->remove($modelo);
            $em->flush();
        }

        return $this->redirectToRoute('home');
    }
}