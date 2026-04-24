<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'modelo')]
class Modelo
{
    #[ORM\Id]
    #[ORM\Column(type:'integer', name:'id')]
    private $modeloId;

    #[ORM\Column(type:'integer', name:'marca')]
    private $marca;

    #[ORM\Column(type: "string", name: "nombre")]
    private $modeloNombre;

    #[ORM\Column(type: "string", name: "foto")]
    private $fotoModelo;

    public function getModeloId() { 
        return $this->modeloId; 
    }

    public function setModeloId($modeloId){
        $this->modeloId = $modeloId;
    }

    public function getMarca() { 
        return $this->marca; 
    }

    public function setFollowed($marca) { 
        $this->marca = $marca; 
    }

    public function getNombreModelo() { 
        return $this->modeloNombre; 
    }

    public function setConfirmed($modeloNombre) { 
        $this->modeloNombre = $modeloNombre; 
    }

    public function getFotoModelo(){
        return $this->fotoModelo;
    }

    public function setFotoModelo($fotoModelo){
        $this->fotoModelo = $fotoModelo;
    }
}