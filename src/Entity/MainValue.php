<?php

namespace App\Entity;

use App\Repository\MainValueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MainValueRepository::class)
 */
class MainValue
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
    private $label;

    /**
     * @ORM\OneToOne(targetEntity=Attribute::class, inversedBy="mainValue", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $attribute;

    /**
     * @ORM\OneToMany(targetEntity=AttributeValue::class, mappedBy="main_value")
     */
    private $attribute_values;


    public function __construct()
    {
        $this->attribute_values = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getAttribute(): ?Attribute
    {
        return $this->attribute;
    }

    public function setAttribute(Attribute $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * @return Collection|AttributeValue[]
     */
    public function getAttributeValues(): Collection
    {
        return $this->attribute_values;
    }

    public function addAttributeValue(AttributeValue $attributeValue): self
    {
        if (!$this->attribute_values->contains($attributeValue)) {
            $this->attribute_values[] = $attributeValue;
            $attributeValue->setMainValue($this);
        }

        return $this;
    }

    public function removeAttributeValue(AttributeValue $attributeValue): self
    {
        if ($this->attribute_values->removeElement($attributeValue)) {
            // set the owning side to null (unless already changed)
            if ($attributeValue->getMainValue() === $this) {
                $attributeValue->setMainValue(null);
            }
        }

        return $this;
    }

}
