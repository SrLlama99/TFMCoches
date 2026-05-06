<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Modelo;

final class MainController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function home(EntityManagerInterface $em): Response
    {
        // $this->denyAccessUnlessGranted("ROLE_USER");

        $modelosEM = $em->getRepository(Modelo::class);

        $mostRated = $modelosEM->findByMostRated();
        // $mostLiked = $modelosEM->findByBest(); // TODO: Add 3 best cars from the db
        // $mostHated
        // $topFromBrands= $modelos->findBestOfBrands();
        
        return $this->render('home/home.html.twig',
            [
                // "mostLiked"=>$mostLiked,
                // "topFromBrands"=>$topFromBrands
                "mostRated"=>$mostRated
            ]);
    }
}
