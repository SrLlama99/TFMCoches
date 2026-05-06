<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'cocheGaraje')]
class cocheGaraje
{
    #[ORM\Id]
    #[ORM\Column(type:'integer', name:'usuario')]
    private $usuario;

    #[ORM\Id]
    #[ORM\Column(type:'integer', name:'coche')]
    private $coche;

    #[ORM\Column(type: "string", name: "notas")]
    private $notas;

    public function getUsuario() { 
        return $this->usuario; 
    }

    public function setUsuario($usuario){
        $this->usuario = $usuario;
    }

    public function getCoche() { 
        return $this->coche; 
    }

    public function setCoche($coche) { 
        $this->coche = $coche; 
    }

    public function getNotas() { 
        return $this->notas; 
    }

    public function setNotas($notas) { 
        $this->notas = $notas; 
    }
}