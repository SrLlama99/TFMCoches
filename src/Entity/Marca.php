<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

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

    public function getidMarca() { 
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
}