<?php

namespace App\Entity;

use App\Repository\PortRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PortRepository::class)
 */
class Port
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=PlantsPorts::class, inversedBy="ports")
     */
    private $plants;

    public function __construct()
    {
        $this->plants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|PlantsPorts[]
     */
    public function getPlants(): Collection
    {
        return $this->plants;
    }

    public function addPlant(PlantsPorts $plant): self
    {
        if (!$this->plants->contains($plant)) {
            $this->plants[] = $plant;
        }
        return $this;
    }

    public function removePlant(PlantsPorts $plant): self
    {
        if ($this->plants->contains($plant)) {
            $this->plants->removeElement($plant);
        }
        return $this;
    }


}
