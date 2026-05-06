<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'motor')]
class Motor
{
    #[ORM\Id]
    #[ORM\Column(type:'integer', name:'id')]
    private $idMotor;

    #[ORM\Column(type:'string', name:'nombre')]
    private $nombreMotor;

    #[ORM\Column(type: "integer", name: "carburante")]
    private $carburante;

    public function getIdMotor() { 
        return $this->idMotor; 
    }

    public function setIdMotor($idMotor){
        $this->idMotor = $idMotor;
    }

    public function getNombreMotor() { 
        return $this->nombreMotor; 
    }

    public function setNombreMotor($nombreMotor) { 
        $this->nombreMotor = $nombreMotor; 
    }

    public function getCarburante() { 
        return $this->carburante; 
    }

    public function setCarburante($carburante) { 
        $this->carburante = $carburante; 
    }
}