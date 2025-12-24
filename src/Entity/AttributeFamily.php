<?php

namespace App\Entity;

use App\Repository\AttributeFamilyRepository;
use App\Entity\PropertyOrAttributeAbstract;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;

#[ORM\Entity(repositoryClass: AttributeFamilyRepository::class)]
#[ORM\Table(name: 'attribute_family')]
class AttributeFamily
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column]
    private ?int $position;

    #[ORM\ManyToOne(targetEntity: Category::class,inversedBy : "families")]
    private $category;

    #[ORM\ManyToOne(targetEntity: AttributeFamily::class,inversedBy : "children")]
    private $parent;

    #[ORM\OneToMany(targetEntity: AttributeFamily::class,mappedBy : "parent")]
    private $children;

    #[ORM\OneToMany(targetEntity: PropertyOrAttribute::class,mappedBy : "family")]
    private $propertyOrAttributes;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->propertyOrAttributes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        if ($this->name === '_'){
            return '';
        }
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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

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
            $propertyOrAttribute->setFamily($this);
        }

        return $this;
    }

    public function removePropertyOrAttribute(PropertyOrAttribute $propertyOrAttribute): self
    {
        if ($this->propertyOrAttributes->removeElement($propertyOrAttribute)) {
            // set the owning side to null (unless already changed)
            if ($propertyOrAttribute->getFamily() === $this) {
                $propertyOrAttribute->setFamily(null);
            }
        }

        return $this;
    }

}
