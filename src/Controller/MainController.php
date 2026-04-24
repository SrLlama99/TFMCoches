<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function home(): Response
    {
        // $this->denyAccessUnlessGranted("ROLE_USER");
        return $this->render('skeleton.html.twig');
    }
}
