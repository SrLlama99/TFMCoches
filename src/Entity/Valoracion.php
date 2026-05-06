<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Coche;

#[ORM\Entity]
#[ORM\Table(name: 'valoraciones')]
class Valoracion
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', name: 'id')]
    private $idValoracion;

    #[ORM\Column(type: 'integer', name: 'usuario_id')]
    private $idUsuario;

    #[ORM\ManyToOne(targetEntity: Coche::class, inversedBy: 'estrellas')]
    #[ORM\JoinColumn(name: 'coche_id', referencedColumnName: 'id', nullable: false)]
    private $idCoche;

    #[ORM\Column(type: 'integer', name: 'estrellas')]
    private $estrellas;

    #[ORM\Column(type: 'string', name: 'comentario')]
    private $comentario;

    #[ORM\Column(type: 'string', name: 'fecha')]
    private $fecha;

    public function getIdValoracion()
    {
        return $this->idValoracion;
    }

    public function setIdValoracion($idValoracion)
    {
        $this->idValoracion = $idValoracion;
    }

    public function getIdCoche(): ?Coche
    {
        return $this->idCoche;
    }

    public function setIdCoche(?Coche $coche): self
    {
        $this->idCoche = $coche;
        return $this;
    }

    public function getNota(): int
    {
        return (int) $this->estrellas;
    }

    public function getEstrellas()
    {
        return $this->estrellas;
    }

    public function setEstrellas($estrellas)
    {
        $this->estrellas = $estrellas;
    }

    public function getComentario()
    {
        return $this->comentario;
    }

    public function setComentario($comentario)
    {
        $this->comentario = $comentario;
    }

    public function getFecha()
    {
        return $this->fecha;
    }

    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;
    }
}