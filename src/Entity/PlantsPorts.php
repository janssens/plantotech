<?php

namespace App\Entity;

use App\Repository\PlantsPortsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlantsPortsRepository::class)
 */
class PlantsPorts
{
    const PLANT_PORT_TYPE_NATURAL = 1;
    const PLANT_PORT_TYPE_POSSIBLE = 2;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Plant::class, inversedBy="ports")
     * @ORM\JoinColumn(nullable=false)
     */
    private $plant;

    /**
     * @ORM\OneToMany(targetEntity=Port::class, mappedBy="plants")
     */
    private $port;

    public function __construct()
    {
        $this->port = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        if (!in_array($type,array(self::PLANT_PORT_TYPE_NATURAL,self::PLANT_PORT_TYPE_POSSIBLE))){
            $type = self::PLANT_PORT_TYPE_POSSIBLE;
        }

        $this->type = $type;

        return $this;
    }

    public function getPlant(): ?Plant
    {
        return $this->plant;
    }

    public function setPlant(?Plant $plant): self
    {
        $this->plant = $plant;

        return $this;
    }

    /**
     * @return Collection|Port[]
     */
    public function getPort(): Collection
    {
        return $this->port;
    }

    public function addPort(Port $port): self
    {
        if (!$this->port->contains($port)) {
            $this->port[] = $port;
            $port->setPlants($this);
        }

        return $this;
    }

    public function removePort(Port $port): self
    {
        if ($this->port->contains($port)) {
            $this->port->removeElement($port);
            // set the owning side to null (unless already changed)
            if ($port->getPlants() === $this) {
                $port->setPlants(null);
            }
        }

        return $this;
    }
}
