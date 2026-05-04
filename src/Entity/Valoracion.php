<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'valoraciones')]
class Valoracion
{
    #[ORM\Id]
    #[ORM\Column(type:'integer', name:'id')]
    private $idValoracion;

    #[ORM\Column(type:'integer', name:'usuario_id')]
    private $idUsuario;

    #[ORM\Column(type: 'integer', name: 'coche_id')]
    private $idCoche;

    #[ORM\Column(type: 'integer', name: 'estrellas')]
    private $estrellas;

    #[ORM\Column(type: 'string', name: 'comentario')]
    private $comentario;

    #[ORM\Column(type: 'string', name: 'fecha')]
    private $fecha;

    public function getIdValoracion() { 
        return $this->idValoracion; 
    }

    public function setIdMarca($idValoracion){
        $this->idValoracion = $idValoracion;
    }

    public function getIdUsuario() { 
        return $this->idUsuario; 
    }

    public function setIdUsuario($idUsuario) { 
        $this->idUsuario = $idUsuario; 
    }

    public function getIdCoche() { 
        return $this->idCoche; 
    }

    public function setIdCoche($idCoche) { 
        $this->idCoche = $idCoche; 
    }

    public function getEstrellas() { 
        return $this->estrellas; 
    }

    public function setEstrellas($estrellas) { 
        $this->estrellas = $estrellas; 
    }

    public function getComentario(){
        return $this->comentario;
    }

    public function setComentario($comentario){
        $this->comentario = $comentario;
    }

    public function getFecha(){
        return $this->fecha;
    }

    public function setFecha($fecha){
        $this->fecha = $fecha;
    }
}