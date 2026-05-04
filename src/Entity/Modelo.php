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

    public function setmodeloId($modeloId){
        $this->modeloId = $modeloId;
    }

    public function getMarca() { 
        return $this->marca; 
    }

    public function setMarca($marca) { 
        $this->marca = $marca; 
    }

    public function getnombreModelo() { 
        return $this->modeloNombre; 
    }

    public function setnombreModelo($modeloNombre) { 
        $this->modeloNombre = $modeloNombre; 
    }

    public function getfotoModelo(){
        return $this->fotoModelo;
    }

    public function setfotoModelo($fotoModelo){
        $this->fotoModelo = $fotoModelo;
    }
}