<?php

namespace App\Entity;

use App\Repository\PlantsInsolationsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlantsInsolationsRepository::class)
 */
class PlantsInsolations
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $ideal;

    /**
     * @ORM\ManyToOne(targetEntity=Plant::class, inversedBy="insolations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $plant;

    /**
     * @ORM\ManyToOne(targetEntity=Insolation::class, inversedBy="plants")
     */
    private $insolation;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdeal(): ?bool
    {
        return $this->ideal;
    }

    public function setIdeal(?bool $ideal): self
    {
        $this->ideal = $ideal;

        return $this;
    }

    public function getInsolation(): Insolation
    {
        return $this->insolation;
    }

    public function setInsolation(Insolation $insolation): self
    {
        $this->insolation = $insolation;
        return $this;
    }

    public function getType(): int
    {
        return $this->getInsolation()->getType();
    }

    public function getPlant(): Plant
    {
        return $this->plant;
    }

    public function setPlant(Plant $plant): self
    {
        $this->plant = $plant;
        return $this;
    }
}
