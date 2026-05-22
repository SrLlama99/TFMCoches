<?php
namespace App\Entity;

use App\Repository\ModeloRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModeloRepository::class)]
#[ORM\Table(name: 'modelo')]
class Modelo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:'integer', name:'id')]
    private $modeloId;

    #[ORM\ManyToOne(targetEntity: Marca::class, inversedBy: 'modelos')]
    #[ORM\JoinColumn(name: 'marca', referencedColumnName: 'id')]
    private Marca $marca;
    
    #[ORM\Column(type: "string", name: "nombre")]
    private $modeloNombre;

    #[ORM\Column(type: "string", name: "foto")]
    private $fotoModelo;

    #[ORM\OneToMany(mappedBy: 'modelo', targetEntity: Coche::class)]
    private Collection $coches;

    public function __construct()
    {
        $this->coches = new ArrayCollection();
    }

    /**
     * @return Collection<int, Coche>
     */
    public function getCoches(): Collection
    {
        return $this->coches;
    }

    public function getMediaValoraciones(): int
    {
        $sumaNotas = 0;
        $totalValoraciones = 0;

        foreach ($this->coches as $coche) {
            foreach ($coche->getEstrellas() as $valoracion) {
                $sumaNotas += $valoracion->getNota();
                $totalValoraciones++;
            }
        }

        if ($totalValoraciones === 0) {
            return 0;
        }

        return (int) round($sumaNotas / $totalValoraciones);
    }

    public function getModeloId(): int { 
        return $this->modeloId; 
    }

    public function setmodeloId(int $modeloId){
        $this->modeloId = $modeloId;
    }

    public function getMarca(): Marca { 
        return $this->marca; 
    }

    public function setMarca(Marca $marca) { 
        $this->marca = $marca; 
    }

    public function getmodeloNombre(): string { 
        return $this->modeloNombre; 
    }

    public function setnombreModelo(string $modeloNombre) { 
        $this->modeloNombre = $modeloNombre; 
    }

    public function getfotoModelo(): ?string{
        return $this->fotoModelo;
    }

    public function setfotoModelo(string $fotoModelo){
        $this->fotoModelo = $fotoModelo;
    }
}