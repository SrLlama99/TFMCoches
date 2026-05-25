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
        $modelosEM = $em->getRepository(Modelo::class);

        $mostRated = $modelosEM->findByMostRated();
        
        $mostLikedLookup = $modelosEM->findByBest(3);
        $mostLiked = [$mostLikedLookup[1], $mostLikedLookup[0], $mostLikedLookup[2]];

        $mostHated = $modelosEM->findByWorst(3);

        return $this->render(
            'home/home.html.twig',
            [
                "mostRated" => $mostRated,
                "mostLiked" => $mostLiked,
                "mostHated" => $mostHated
                // "topFromBrands"=>$topFromBrands
            ]
        );
    }
}
