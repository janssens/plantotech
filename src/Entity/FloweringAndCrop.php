<?php

namespace App\Entity;

use App\Repository\FloweringAndCropRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FloweringAndCropRepository::class)
 */
class FloweringAndCrop
{
    const TYPE_CROP = 1;
    const TYPE_FLOWERING = 2;

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
     * @ORM\Column(type="smallint")
     */
    private $month;

    /**
     * @ORM\ManyToOne(targetEntity=Plant::class, inversedBy="floweringAndCrops")
     * @ORM\JoinColumn(nullable=false)
     */
    private $plant;

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

    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function setMonth(int $month): self
    {
        $this->month = $month;

        return $this;
    }

    public function getPlant(): ?Plant
    {
        return $this->plants;
    }

    public function setPlant(?Plant $plant): self
    {
        $this->plant = $plant;

        return $this;
    }
}
