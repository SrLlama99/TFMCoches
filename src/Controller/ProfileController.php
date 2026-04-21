<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile/{name}', name: 'profile')]
    public function home(EntityManagerInterface $em, Request $request, $name)
    {
        // $this->denyAccessUnlessGranted("ROLE_USER");
        $rep = $em->getRepository(Usuario::class);

        // Sacar el usuario
        $usuario = $rep->findOneBy($name);


        return $this->render('profile.html.twig', ['user' => $usuario]);
    }
}
