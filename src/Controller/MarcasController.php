<?php
namespace App\Controller;

use App\Entity\Marca;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MarcasController extends AbstractController
{
    #[Route('/brands', name: 'marca_index')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $letter = $request->query->get('letter');

        $repo = $doctrine->getRepository(Marca::class);

        // total count
        $qbCount = $repo->createQueryBuilder('m')->select('COUNT(m.idMarca)');
        if ($letter) {
            $qbCount->where('m.nombreMarca LIKE :p')->setParameter('p', $letter . '%');
        }
        $total = (int) $qbCount->getQuery()->getSingleScalarResult();

        // page items
        $qb = $repo->createQueryBuilder('m')->orderBy('m.nombreMarca', 'ASC');
        if ($letter) {
            $qb->where('m.nombreMarca LIKE :p')->setParameter('p', $letter . '%');
        }
        $brands = $qb->getQuery()->getResult();

        return $this->render('marca/index.html.twig', [
            'brands' => $brands,
            'letter' => $letter,
        ]);
    }
}
