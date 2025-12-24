<?php

namespace App\Entity;

use App\Repository\AssociationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssociationRepository::class)]
#[ORM\Table(name: 'association')]
class Association
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type:Types::SMALLINT)]
    private int $type;

    #[ORM\Column(length:100)]
    private ?string $comment;

    #[ORM\JoinColumn(nullable:false)]
    #[ORM\ManyToOne(targetEntity: Plant::class,inversedBy : "associations")]
    private $plant1;

    #[ORM\ManyToOne(targetEntity: Plant::class,inversedBy : "plants")]
    #[ORM\JoinColumn(nullable:false)]
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
