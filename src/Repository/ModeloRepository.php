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

    public function findByBestRated()
    {
        $result = $this->getEntityManager()->createQueryBuilder()
            ->select('m', 'SUM(v.estrellas) as totalEstrellas', 'COUNT(v)')
            ->from('Modelo', 'm')
            ->join('Valoracion', 'v', 'WITH', 'm.id = v.idCoche')
            ->groupBy('m.id')
            ->orderBy('');
        return $result;
    }
    public function findByMostRated($limit = 3)
    {
        $result = $this->getEntityManager()->createQueryBuilder()
            ->select('m as modelo', 'COUNT(v.idCoche) as totalRates')
            ->from(\App\Entity\Modelo::class, 'm')
            ->join(\App\Entity\Valoracion::class, 'v', 'WITH', 'm.modeloId = v.idCoche')
            ->groupBy('m.modeloId')
            ->orderBy('totalRates', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
        return $result;
    }
}
