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
     * @ORM\ManyToMany(targetEntity=Plant::class, inversedBy="mainValues")
     */
    private $plants;

    /**
     * @ORM\OneToOne(targetEntity=AttributeValue::class, inversedBy="mainValue", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $attribute_value;

    /**
     * @ORM\ManyToOne(targetEntity=Attribute::class, inversedBy="mainValues")
     * @ORM\JoinColumn(nullable=false)
     */
    private $attribute;


    public function __construct()
    {
        $this->plants = new ArrayCollection();
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
        $this->plants->removeElement($plant);

        return $this;
    }

    public function getAttributeValue(): ?AttributeValue
    {
        return $this->attribute_value;
    }

    public function setAttributeValue(AttributeValue $attribute_value): self
    {
        $this->attribute_value = $attribute_value;

        return $this;
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

}
