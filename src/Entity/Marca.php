<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'marca')]
class Marca
{
    #[ORM\Id]
    #[ORM\Column(type:'integer', name:'id')]
    private $idMarca;

    #[ORM\Column(type:'string', name:'nombre')]
    private $nombreMarca;

    #[ORM\Column(type: "string", name: "url")]
    private $urlMarca;

    public function getIdMarca() { 
        return $this->idMarca; 
    }

    public function setIdMarca($idMarca){
        $this->idMarca = $idMarca;
    }

    public function getNombreMarca() { 
        return $this->nombreMarca; 
    }

    public function setNombreMarca($nombreMarca) { 
        $this->nombreMarca = $nombreMarca; 
    }

    public function getUrlMarca() { 
        return $this->urlMarca; 
    }

    public function setConfirmed($urlMarca) { 
        $this->urlMarca = $urlMarca; 
    }
}