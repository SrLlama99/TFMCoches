<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'marca')]
class Marca
{
    #[ORM\Id]
    #[ORM\Column(type:'integer', name:'id')]
    private $idMarca;

    #[ORM\Column(type:'string', name:'nombre')]
    private $nombreMarca;

    #[ORM\Column(type: 'string', name: 'url')]
    private $urlMarca;

    #[ORM\Column(type: 'string', name: 'logourl')]
    private $urlLogo;

    #[ORM\OneToMany(targetEntity: Modelo::class, mappedBy: 'marca')]
    private Collection $modelos;
    
    public function __construct()
    {
        $this->modelos = new ArrayCollection();
    }

    /**
     * @return Collection<int, Modelo>
     */
    public function getModelos(): Collection
    {
        return $this->modelos;
    }
    public function getIdMarca() { 
        return $this->idMarca; 
    }

    public function setidMarca($idMarca){
        $this->idMarca = $idMarca;
    }

    public function getnombreMarca() { 
        return $this->nombreMarca; 
    }

    public function setnombreMarca($nombreMarca) { 
        $this->nombreMarca = $nombreMarca; 
    }

    public function geturlMarca() { 
        return $this->urlMarca; 
    }

    public function seturlMarca($urlMarca) { 
        $this->urlMarca = $urlMarca; 
    }

    public function geturlLogo() { 
        return $this->urlLogo; 
    }   

    public function seturlLogo($urlLogo) { 
        $this->urlLogo = $urlLogo; 
    }
    public function getId(): int { 
        return $this->idMarca; 
    }
    public function setId(int $id) { 
        return $this->idMarca = $id; 
    }
    public function getImage(): string { 
        return $this->urlLogo; 
    }
    public function setImage(string $urlLogo) { 
        return $this->urlLogo = $urlLogo; 
    }
}