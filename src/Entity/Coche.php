<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'coche')]
class Coche
{
    #[ORM\Id]
    #[ORM\Column(type:'integer', name:'id')]
    private $cocheId;

    #[ORM\Column(type:'integer', name:'modelo')]
    private $modelo;

    #[ORM\Column(type: "integer", name: "motor")]
    private $motor;

    #[ORM\Column(type: "string", name: "color")]
    private $cocheColor;

    #[ORM\Column(type: "integer", name: "año")]
    private $cocheAnio;

    #[ORM\Column(type: "string", name: "transmision")]
    private $cocheTransmision;

    public function getCocheId() { 
        return $this->cocheId; 
    }

    public function setCocheId($cocheId){
        $this->cocheId = $cocheId;
    }

    public function getModelo() { 
        return $this->modelo; 
    }

    public function setModelo($modelo) { 
        $this->modelo = $modelo; 
    }

    public function getMotor() { 
        return $this->motor; 
    }

    public function setMotor($motor) { 
        $this->motor = $motor; 
    }

    public function getCocheColor(){
        return $this->cocheColor;
    }

    public function setCocheColor($cocheColor){
        $this->cocheColor = $cocheColor;
    }

    public function getCocheAnio(){
        return $this->cocheAnio;
    }

    public function setCocheAnio($cocheAnio){
        $this->cocheAnio = $cocheAnio;
    }

    public function getCocheTransmision(){
        return $this->cocheTransmision;
    }

    public function setCocheTransmision($cocheTransmision){
        $this->cocheTransmision = $cocheTransmision;
    }
}