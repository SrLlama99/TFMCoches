<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Users;
use App\Entity\Coche;
use App\Entity\Modelo;
use App\Entity\Marca;

final class MarcaController extends AbstractController
{
    #[Route('/marca/{name}', name: 'marca')]
    public function home(EntityManagerInterface $em, Request $request, string $name): Response
    {
        // Define repositories to use
        $usersRepo = $em->getRepository(Users::class);
        $carsRepo = $em->getRepository(Coche::class);
        $modelRepo = $em->getRepository(Modelo::class);
        $marcaRepo = $em->getRepository(Marca::class);

        $marca = $marcaRepo->findOneBy(['nombreMarca' => strtoupper($name)]);
        $model = $modelRepo->findBy(['marca' => $marca]);

        return $this->render('marca/marca.html.twig', [
            'marca' => $marca,
            'modelos' => $model
        ]);
    }
}