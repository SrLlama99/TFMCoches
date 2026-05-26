<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

final class CICDController extends AbstractController
{
    #[Route('/updateRepo', name: 'updateRepo')]
    public function cicdIsMyPassion()
    {
        $exitCode = null;
        exec("git pull", result_code: $exitCode);
        if ($exitCode == 0) {
            return $this->json(['ok' => true], 200);
        }
        return $this->json(['ok' => false], 500);
    }
}
