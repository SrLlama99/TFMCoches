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
use App\Entity\Valoracion;
use App\Entity\Users;
use App\Entity\FotoGaraje;
use App\Entity\cocheGaraje;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


final class ProfilesController extends AbstractController
{
    
    #[Route('/valoracion/{id}/delete', name: 'valoracion_delete', methods: ['POST'])]
    public function deleteValoracion(EntityManagerInterface $em, int $id): JsonResponse
    {
        $user = $this->getUser();
        // Only admins may delete valoraciones
        if (! $this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['success' => false, 'error' => 'Forbidden'], 403);
        }

        $repo = $em->getRepository(Valoracion::class);
        $v = $repo->find($id);
        if (! $v) {
            return new JsonResponse(['success' => false, 'error' => 'Not found'], 404);
        }

        try {
            $em->remove($v);
            $em->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }

        return new JsonResponse(['success' => true, 'deleted' => $id]);
    }
    
    #[Route('/garage/{name}', name: 'garage')]
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

        $usuarioId = $usuario?->getUserId();

        if ($usuarioId === null) {
            throw $this->createNotFoundException('Usuario inválido');
        }

        $registrosGaraje = $carsGarageRepo->findBy(['usuario' => $usuarioId]);

        $cochesDelUsuario = [];

        foreach ($registrosGaraje as $registro) {
            $coche = $registro->getCoche();

            if (is_int($coche) || is_string($coche)) {
                $coche = $carsRepo->find((int) $coche);
            }

            if (!$coche) {
                continue;
            }

            // Get model: it may be an entity or an id
            $modelo = $coche?->getModelo() ?? null;
            if ($modelo && !is_object($modelo)) {
                $modelo = $modelRepo->find($modelo);
            }

            // Get brand from the model: it may be an entity or an id
            $marca = null;
            if ($modelo) {
                $marcaVal = is_object($modelo) ? ($modelo->getMarca()->getIdMarca() ?? null) : $modelo;
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

        // Load photos for each cocheGaraje entry so templates can show them
        $fotoRepo = $em->getRepository(FotoGaraje::class);
        foreach ($cochesDelUsuario as &$entry) {
            $cocheEntity = $entry['coche'];
            $cocheId = is_object($cocheEntity) ? ($cocheEntity->getcocheId() ?? null) : null;
            $photos = [];
            if ($cocheId !== null) {
                $fotoEntities = $fotoRepo->findBy(['poseedor' => $usuario, 'coche' => $cocheId]);
                foreach ($fotoEntities as $f) {
                    if (is_object($f)) {
                        $photos[] = ['id' => $f->getId(), 'url' => $f->getUrl()];
                    }
                }
            }
            $entry['photos'] = $photos;
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

            $cid = $vcoche?->getcocheId() ?? $vcoche?->getId() ?? null;
            if ($cid === null) continue;
            if (in_array($cid, $seen, true)) continue;
            $seen[] = $cid;

            $vmodelo = null;
            $vmodelo = $vcoche?->getModelo() ?? null;
            if ($vmodelo && !is_object($vmodelo)) {
                $vmodelo = $modelRepo->find($vmodelo);
            }

            $vmarca = null;
            if ($vmodelo) {
                $marcaVal = is_object($vmodelo) ? ($vmodelo->getMarca()->getIdMarca() ?? null) : $vmodelo;
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

            $vmodelo = $vcoche?->getModelo() ?? null;
            if ($vmodelo && !is_object($vmodelo)) {
                $vmodelo = $modelRepo->find($vmodelo);
            }

            $vmarca = null;
            if ($vmodelo) {
                $marcaVal = is_object($vmodelo) ? ($vmodelo->getMarca()->getIdMarca() ?? null) : $vmodelo;
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
                    'id' => $v->getIdValoracion(),
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

    #[Route('/garaje/{cocheId}/add-photos', name: 'garaje_add_photos', methods: ['POST'])]
    public function addGarajePhotos(EntityManagerInterface $em, int $cocheId, Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) return new JsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);

        $files = $request->files->get('photos');
        if (empty($files)) return new JsonResponse(['success' => false, 'error' => 'No files provided'], 400);

        $uploaded = [];
        $persisted = [];
        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/assets/images/cars/';

        foreach ($files as $idx => $file) {
            if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                $ext = strtolower($file->getClientOriginalExtension());
                $allowed = ['png','jpg','jpeg','webp'];
                if (!in_array($ext, $allowed)) continue;
                $newName = 'garaje_' . $user->getUserId() . '_' . $cocheId . '_' . time() . $idx . $ext;
                    try {
                        $file->move($uploadDir, $newName);
                        $foto = new FotoGaraje();
                        $foto->setPoseedor($user);
                        $foto->setCoche($cocheId);
                        $foto->setUrl($newName);
                        $em->persist($foto);
                        $uploaded[] = $newName;
                        $persisted[] = $foto;
                    } catch (\Exception $e) {
                        // skip failed
                    }
            }
        }
        $em->flush();

        $uploadedInfo = [];
        foreach ($persisted as $p) {
            if (is_object($p)) {
                $uploadedInfo[] = ['id' => $p->getId(), 'url' => $p->getUrl(), 'urlPublic' => '/assets/images/cars/' . $p->getUrl()];
            }
        }

        return new JsonResponse(['success' => true, 'uploaded' => $uploadedInfo]);
    }

    #[Route('/garaje/photo/{photoId}/delete', name: 'garaje_delete_photo', methods: ['POST'])]
    public function deleteGarajePhoto(EntityManagerInterface $em, int $photoId, Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) return new JsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);

        $fotoRepo = $em->getRepository(FotoGaraje::class);
        $foto = $fotoRepo->find($photoId);
        if (!$foto) return new JsonResponse(['success' => false, 'error' => 'Not found'], 404);

        // Check owner
        $poseedor = $foto->getPoseedor();
        $poseedorId = is_object($poseedor) ? ($poseedor->getUserId() ?? $poseedor->getId() ?? null) : null;
        $userId = $user->getUserId(); 
        if ($poseedorId !== $userId) {
            return new JsonResponse(['success' => false, 'error' => 'Forbidden'], 403);
        }

        $fileName = $foto->getUrl();
        try {
            $em->remove($foto);
            $em->flush();
            // remove file from disk
            $path = $this->getParameter('kernel.project_dir') . '/public/assets/images/cars/' . $fileName;
            if (file_exists($path)) @unlink($path);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }

        return new JsonResponse(['success' => true, 'deleted' => $photoId]);
    }

    #[Route('/garage/{name}/update', name: 'profile_update', methods: ['POST'])]
    public function updateProfile(EntityManagerInterface $em, string $name, Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (! $user) {
            return new JsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        $usersRepo = $em->getRepository(Users::class);
        $usuario = $usersRepo->findOneBy(['UserName' => strtolower($name)]);
        if (! $usuario) {
            return new JsonResponse(['success' => false, 'error' => 'Usuario no encontrado'], 404);
        }

        // Ensure the logged user is updating their own profile
        $currentId = $user?->getUserId();
        $targetId = $usuario?->getUserId();
        if ($currentId === null || $targetId === null || $currentId !== $targetId) {
            return new JsonResponse(['success' => false, 'error' => 'Forbidden'], 403);
        }

        $username = strtolower(trim($request->request->get('username', '')));
        $email = trim($request->request->get('email', ''));

        $oldName = $usuario->getUserName();

        if ($username) {
            $usuario->setUserName($username);
        }
        if ($email) {
            $usuario->setUserMail($email);
        }

        // Handle uploaded profile image if provided
        try {
            $file = $request->files->get('garaje_image');
            if ($file) {
                $uploadDir = $this->getParameter('kernel.project_dir') . '/public/assets/images/avatar';
                $ext = $file->guessExtension() ?: 'bin';
                $newName = 'profile_' . ($targetId ?? time()) . '_' . time() . '.' . $ext;
                $file->move($uploadDir, $newName);
                $usuario->setProfilePic($newName);
            }
        } catch (\Exception $e) {

        }

        $em->persist($usuario);
        $em->flush();

        $response = ['success' => true, 'username' => $usuario->getUserName(), 'email' => $usuario->getUserMail()];
        $response['profilePic'] = $usuario?->getProfilePic() ?? null;
        // provide a full public URL for convenience in the client
        $response['profilePicUrl'] = $response['profilePic'] ? '/assets/images/avatar/' . $response['profilePic'] : null;
        // If username changed, instruct client to redirect to the new profile URL
        if ($oldName !== $usuario->getUserName()) {
            $response['redirect'] = $this->generateUrl('garage', ['name' => strtolower($usuario->getUserName())]);
        }

        return new JsonResponse($response);
    }

   #[Route('/deleteAccount/{id}', name: 'app_borrarCuenta')]
    public function delete(EntityManagerInterface $em,TokenStorageInterface $tokenStorage,int $id){
        $userRepo = $em->getRepository(Users::class);
        $valorRepo = $em->getRepository(Valoracion::class);
        $currentUser = $userRepo->findOneBy(['UserId' => $id]);
        if ($currentUser) {
            $valoraciones = $valorRepo->findBy(['idUsuario' => $currentUser]);
            foreach ($valoraciones as $v) {
                $em->remove($v);
            }
            $em->remove($currentUser);
            $em->flush();
            $tokenStorage->setToken(null);
        }
        return $this->redirectToRoute('app_login');
    }
}
