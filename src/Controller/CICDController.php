<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

final class CICDController extends AbstractController
{
    #[Route('/updateRepo', name: 'updateRepo')]
    public function cicdIsMyPassion(){
        exec("git pull");
    }
}
