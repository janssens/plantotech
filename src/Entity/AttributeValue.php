<?php

namespace App\Entity;

use App\Repository\AttributeValuesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AttributeValuesRepository::class)
 */
class AttributeValue
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Attribute::class, inversedBy="availableValues")
     * @ORM\JoinColumn(nullable=false)
     */
    private $attribute;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $value;

    /**
     * @ORM\ManyToMany(targetEntity=Plant::class, inversedBy="attributes")
     */
    private $plants;

    /**
     * @ORM\OneToOne(targetEntity=MainValue::class, mappedBy="attribute_value", cascade={"persist", "remove"})
     */
    private $mainValue;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $code;

    public function __construct()
    {
        $this->plants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAttribute(): ?Attribute
    {
        return $this->attribute;
    }

    public function setAttribute(?Attribute $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

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
            $plant->addAttributeValue($this);
        }

        return $this;
    }

    public function removePlant(Plant $plant): self
    {
        if ($this->plants->contains($plant)) {
            $this->plants->removeElement($plant);
            $plant->removeAttributeValue($this);
        }

        return $this;
    }

    public function getMainValue(): ?MainValue
    {
        return $this->mainValue;
    }

    public function setMainValue(MainValue $mainValue): self
    {
        $this->mainValue = $mainValue;

        // set the owning side of the relation if necessary
        if ($mainValue->getAttributeValue() !== $this) {
            $mainValue->setAttributeValue($this);
        }

        return $this;
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

}
