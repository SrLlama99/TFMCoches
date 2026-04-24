<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'combustible')]
class Follow
{
    #[ORM\Id]
    #[ORM\Column(type:'integer', name:'id')]
    private $idCombustible;

    #[ORM\Column(type:'string', name:'nombre')]
    private $nombreCombustible;

    public function getIdCombustible() { 
        return $this->idCombustible; 
    }

    public function setIdCombustible($idCombustible){
        $this->idCombustible = $idCombustible;
    }

    public function getNombreCombustible() { 
        return $this->nombreCombustible; 
    }

    public function setFollowed($nombreCombustible) { 
        $this->nombreCombustible = $nombreCombustible; 
    }
}