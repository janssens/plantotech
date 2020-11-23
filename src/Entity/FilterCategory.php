<?php

namespace App\Entity;

use App\Repository\FilterCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FilterCategoryRepository::class)
 */
class FilterCategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=PropertyOrAttribute::class, mappedBy="filterCategory")
     */
    private $propertyOrAttributes;

    public function __construct()
    {
        $this->propertyOrAttributes = new ArrayCollection();
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
     * @return Collection|PropertyOrAttribute[]
     */
    public function getPropertyOrAttributes(): Collection
    {
        return $this->propertyOrAttributes;
    }

    public function addPropertyOrAttribute(PropertyOrAttribute $propertyOrAttribute): self
    {
        if (!$this->propertyOrAttributes->contains($propertyOrAttribute)) {
            $this->propertyOrAttributes[] = $propertyOrAttribute;
            $propertyOrAttribute->setFilterCategory($this);
        }

        return $this;
    }

    public function removePropertyOrAttribute(PropertyOrAttribute $propertyOrAttribute): self
    {
        if ($this->propertyOrAttributes->removeElement($propertyOrAttribute)) {
            // set the owning side to null (unless already changed)
            if ($propertyOrAttribute->getFilterCategory() === $this) {
                $propertyOrAttribute->setFilterCategory(null);
            }
        }

        return $this;
    }
}
