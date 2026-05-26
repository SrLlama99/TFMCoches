<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

final class CarsController extends AbstractController
{
    #[Route('/updateRepo', name: 'updateRepo')]
    public function cicdIsMyPassion(){
        return $this->json(['rid'=>dirname(__DIR__)]);
    }
}
