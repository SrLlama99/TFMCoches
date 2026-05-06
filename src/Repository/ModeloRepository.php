<?php

namespace App\Repository;

use App\Entity\Modelo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ModeloRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Modelo::class);
    }

    public function findByWorst(int $limit = 1, \App\Entity\Marca $brand = null, string $order = 'ASC')
    {
        return $this->findByBest($limit, $brand, $order);
    }
    public function findByBest(int $limit = 1, \App\Entity\Marca $brand = null, string $order = 'DESC')
    {
        if ($brand) { //This bs returns the best from the brand provided
            return $this->getEntityManager()->createQueryBuilder()
                ->select('m', 'AVG(v.estrellas) as media')
                ->from(\App\Entity\Modelo::class, 'm')
                ->join(\App\Entity\Valoracion::class, 'v', 'WITH', 'm.modeloId = v.idCoche')
                ->groupBy('m.modeloId')
                ->where('m.marca = :marca')
                ->setParameter('marca', $brand->getIdMarca())
                ->orderBy('media', $order)
                ->setMaxResults($limit)
                ->getQuery()->getResult();
        }
        return
            $this->getEntityManager()->createQueryBuilder()
            ->select('m', 'AVG(v.estrellas) as media')
            ->from(\App\Entity\Modelo::class, 'm')
            ->join(\App\Entity\Valoracion::class, 'v', 'WITH', 'm.modeloId = v.idCoche')
            ->groupBy('m.modeloId')
            ->orderBy('media', $order)
            ->setMaxResults($limit)
            ->getQuery()->getResult();
    }
    public function findByMostRated($limit = 3)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('m as modelo', 'COUNT(v.idCoche) as totalRates')
            ->from(\App\Entity\Modelo::class, 'm')
            ->join(\App\Entity\Valoracion::class, 'v', 'WITH', 'm.modeloId = v.idCoche')
            ->groupBy('m.modeloId')
            ->orderBy('totalRates', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
