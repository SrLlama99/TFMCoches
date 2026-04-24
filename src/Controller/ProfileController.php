<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Users;
use App\Entity\Coche;
use App\Entity\cocheGaraje;

final class ProfileController extends AbstractController
{
    #[Route('/profile/{name}', name: 'profile')]
    public function home(EntityManagerInterface $em, Request $request, string $name): Response
    {
        $usersRepo = $em->getRepository(Users::class);
        $carsGarageRepo = $em->getRepository(cocheGaraje::class);
        $carsRepo = $em->getRepository(Coche::class);

        $usuario = $usersRepo->findOneBy(['UserName' => $name]);

        $cochesDelUsuario = [];
        $registrosGaraje = $carsGarageRepo->findBy(['usuario' => $usuario]);

        foreach ($registrosGaraje as $registro) {
            $cochesDelUsuario[] = $registro->getCoche();
        }

        return $this->render('profile/profile.html.twig', [
            'usuario' => $usuario,
            'coches' => $cochesDelUsuario
        ]);
    }

    #[Route('/new/{name}', name: 'new')]
    public function new(EntityManagerInterface $em, string $name): Response
    {
        $newUser = new Users();
        $newUser->setUserName($name);
        $newUser->setUserMail($name . '@test.com');
        $newUser->setUserPassword($name . "1234");
        $newUser->setAdmin(0);

        $em->persist($newUser);
        $em->flush();
        
        return $this->json(['status' => 'User created!']);
    }
}