<?php

namespace App\Entity;

use App\Repository\AssociationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AssociationRepository::class)
 */
class Association
{
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
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity=Plant::class, inversedBy="associations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $plant1;

    /**
     * @ORM\ManyToOne(targetEntity=Plant::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $plant2;

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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getPlant1(): ?Plant
    {
        return $this->plant1;
    }

    public function setPlant1(?Plant $plant1): self
    {
        $this->plant1 = $plant1;

        return $this;
    }

    public function getPlant2(): ?Plant
    {
        return $this->plant2;
    }

    public function setPlant2(?Plant $plant2): self
    {
        $this->plant2 = $plant2;

        return $this;
    }
}
