<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function home(EntityManagerInterface $em, Request $request)
    {
        // Repositorios
        $users = $em->getRepository(Users::class);
        $carsGarage = $em->getRepository(cocheGaraje::class);
        $cars = $em->getRepository(Coche::class);

        // Sacar el usuario
        $usuario = $users->findOneBy(['userName' => $name]);

        // Sacar Id del Usuario
        if($usuario){
            $idUsuario = $usuario->getId();
        }

        // Sacar los coches en el garaje del usuario
        $cochesUsu = $carsGarage->findBy(['usuario' => $idUsuario]);

        // Sacar los coches mejor valorados por el usuario

        return $this->render('profile/profile.html.twig');
    }
}
