<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Users;

#[ORM\Entity]
#[ORM\Table(name: 'fotoscochegaraje')]
class FotoGaraje
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(name: 'poseedor', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private $poseedor;

    // Reference to coche via its 'coche' column (stored as integer fk)
    #[ORM\Column(type: 'integer', name: 'coche', nullable: false)]
    private $coche;

    #[ORM\Column(type: 'string', length: 250, name: 'url', nullable: true)]
    private $url;

    public function getPoseedor(): ?Users
    {
        return $this->poseedor;
    }

    public function setPoseedor(?Users $poseedor): self
    {
        $this->poseedor = $poseedor;
        return $this;
    }

    public function getCoche(): ?int
    {
        return $this->coche;
    }

    public function setCoche(int $coche): self
    {
        $this->coche = $coche;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;
        return $this;
    }
}
