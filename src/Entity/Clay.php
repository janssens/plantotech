<?php

namespace App\Entity;

use App\Repository\ClayRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @ORM\Entity(repositoryClass=ClayRepository::class)
 */
class Clay
{
    const SCALE_FIVE  = 5;
    const SCALE_TEN  = 10;
    const SCALE_TWENTY  = 20;
    const SCALE_THIRTY  = 30;
    const SCALE_MORE_THAN_THIRTY  = 31;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint")
     */
    private $scale;

    /**
     * @ORM\ManyToMany(targetEntity=Plant::class, inversedBy="clays")
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

    public function getScale(): ?int
    {
        return $this->scale;
    }

    public function setScale(int $scale): self
    {
        $this->scale = $scale;

        return $this;
    }

    /**
     * @return Collection|Plant[]
     */
    public function getPlants(): Collection
    {
        return $this->plants;
    }

    public function addPlant(Plant $plant): self
    {
        if (!$this->plants->contains($plant)) {
            $this->plants[] = $plant;
        }

        return $this;
    }

    public function removePlant(Plant $plant): self
    {
        if ($this->plants->contains($plant)) {
            $this->plants->removeElement($plant);
        }

        return $this;
    }
}
