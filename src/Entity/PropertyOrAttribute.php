<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\InheritanceType;

#[ORM\Entity]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name:'discr',type:Types::STRING)]
class PropertyOrAttribute
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private $id;

    #[ORM\Column(length:50)]
    private string $code;

    #[ORM\Column(length:50)]
    private string $name;

    #[ORM\Column]
    private ?int $position;

    #[ORM\ManyToOne(targetEntity: FilterCategory::class,inversedBy : "propertyOrAttributes")]
    private $filterCategory;

    #[ORM\ManyToOne(targetEntity: AttributeFamily::class,inversedBy : "propertyOrAttributes")]
    private $family;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getFamily(): ?AttributeFamily
    {
        return $this->family;
    }

    public function setFamily(?AttributeFamily $family): self
    {
        $this->family = $family;

        return $this;
    }

    public function getFilterCategory(): ?FilterCategory
    {
        return $this->filterCategory;
    }

    public function setFilterCategory(?FilterCategory $filterCategory): self
    {
        $this->filterCategory = $filterCategory;

        return $this;
    }

    public function isAttribute(): bool
    {
        return false;
    }

    public function isProperty(): bool
    {
        return false;
    }

    public static function compare(self $a, self $b)
    {
        return $a->getPosition() > $b->getPosition();
    }
}
