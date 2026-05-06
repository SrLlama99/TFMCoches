<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Valoracion;
use App\Entity\Modelo;
use App\Entity\Motor; // Importamos la entidad Motor
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'coche')]
class Coche
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', name: 'id')]
    private $cocheId;

    #[ORM\ManyToOne(targetEntity: Modelo::class, inversedBy: 'coches')]
    #[ORM\JoinColumn(name: 'modelo', referencedColumnName: 'id', nullable: false)]
    private $modelo;
    
    #[ORM\ManyToOne(targetEntity: Motor::class)]
    #[ORM\JoinColumn(name: 'motor', referencedColumnName: 'id', nullable: false)]
    private $motor;

    #[ORM\Column(type: "string", name: "color")]
    private $cocheColor;

    #[ORM\Column(type: "integer", name: "año")]
    private $cocheAnio;

    #[ORM\Column(type: "string", name: "transmision")]
    private $cocheTransmision;

    #[ORM\OneToMany(mappedBy: 'idCoche', targetEntity: Valoracion::class)]
    private Collection $estrellas;

    public function __construct()
    {
        $this->estrellas = new ArrayCollection();
    }

    public function getcocheId()
    {
        return $this->cocheId;
    }

    public function setcocheId($cocheId)
    {
        $this->cocheId = $cocheId;
    }

    public function getModelo(): ?Modelo
    {
        return $this->modelo;
    }

    public function setModelo(?Modelo $modelo): self
    {
        $this->modelo = $modelo;
        return $this;
    }

    public function getMotor(): ?Motor
    {
        return $this->motor;
    }

    public function setMotor(?Motor $motor): self
    {
        $this->motor = $motor;
        return $this;
    }

    public function getCocheColor()
    {
        return $this->cocheColor;
    }

    public function setCocheColor($cocheColor)
    {
        $this->cocheColor = $cocheColor;
    }

    public function getCocheAnio()
    {
        return $this->cocheAnio;
    }

    public function setCocheAnio($cocheAnio)
    {
        $this->cocheAnio = $cocheAnio;
    }

    public function getCocheTransmision()
    {
        return $this->cocheTransmision;
    }

    public function setCocheTransmision($cocheTransmision)
    {
        $this->cocheTransmision = $cocheTransmision;
    }

    /**
     * @return Collection<int, Valoracion>
     */
    public function getEstrellas(): Collection
    {
        return $this->estrellas;
    }

    public function getMediaValoraciones(): int
    {
        if ($this->estrellas->isEmpty()) {
            return 0;
        }

        $suma = 0;
        foreach ($this->estrellas as $valoracion) {
            $suma += $valoracion->getNota();
        }

        return (int) round($suma / count($this->estrellas));
    }
}