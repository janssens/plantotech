<?php

namespace App\Entity;

use App\Repository\InsolationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InsolationRepository::class)
 */
class Insolation
{
    const TYPE_SUN = 10;
    const TYPE_HALF_SHADE = 5;
    const TYPE_SHADE = 1;

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
     * @ORM\OneToMany(targetEntity=PlantsInsolations::class, mappedBy="insolation")
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

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|PlantsInsolations[]
     */
    public function getPlants(): Collection
    {
        return $this->plants;
    }

    public function addPlant(PlantsInsolations $plant): self
    {
        if (!$this->plants->contains($plant)) {
            $this->plants[] = $plant;
        }

        return $this;
    }

    public function removePlant(PlantsInsolations $plant): self
    {
        if ($this->plants->contains($plant)) {
            $this->plants->removeElement($plant);
        }

        return $this;
    }
}
