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
    #[ORM\GeneratedValue]
    #[ORM\Column(type:'integer', name:'id')]
    private int $idMarca;

    #[ORM\Column(type:'string', name:'nombre', unique: true)]
    private string $nombreMarca;

    #[ORM\Column(type: 'string', name: 'url')]
    private string $urlMarca;

    #[ORM\Column(type: 'string', name: 'logourl')]
    private string $urlLogo;

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

    public function setidMarca(int $idMarca){
        $this->idMarca = $idMarca;
    }

    public function getnombreMarca() { 
        return $this->nombreMarca; 
    }

    public function setnombreMarca(string $nombreMarca) { 
        $this->nombreMarca = $nombreMarca; 
    }

    public function geturlMarca() { 
        return $this->urlMarca; 
    }

    public function seturlMarca(string $urlMarca) { 
        $this->urlMarca = $urlMarca; 
    }

    public function geturlLogo() { 
        return $this->urlLogo;
    }   
    public function getAsset(): string{
        if($this->getImage() == null || $this->getImage() == ""){
            return '/assets/images/missingPicture.jpg';
        } else {
            return '/assets/images/brands/'.$this->getImage();
        }
    }
    public function seturlLogo(string $urlLogo) { 
        $this->urlLogo = $urlLogo; 
    }
    public function getId(): int { 
        return $this->idMarca; 
    }
    public function setId(int $id) { 
        return $this->idMarca = $id; 
    }
    public function getImage(): string { 
        return $this->geturlLogo();
    }
    public function setImage(string $urlLogo) { 
        return $this->seturlLogo($urlLogo); 
    }
}