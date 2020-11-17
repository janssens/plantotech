<?php

namespace App\Entity;

use App\Repository\AttributeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AttributeRepository::class)
 */
class Attribute
{

    const TYPE_NONE  = 1;
    const TYPE_SINGLE  = 2;
    const TYPE_MULTIPLE = 3;
    const TYPE_UNIQUE = 4;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="smallint")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=AttributeFamily::class, inversedBy="attributes")
     */
    private $family;

    /**
     * @ORM\OneToMany(targetEntity=AttributeValue::class, mappedBy="attribute", orphanRemoval=true)
     */
    private $availableValues;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $code;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position;

    /**
     * @ORM\OneToMany(targetEntity=MainValue::class, mappedBy="attribute", orphanRemoval=true)
     */
    private $mainValues;

    public function __construct()
    {
        $this->availableValues = new ArrayCollection();
        $this->mainValues = new ArrayCollection();
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

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function isTypeNone() : bool
    {
        return $this->getType() === self::TYPE_NONE;
    }

    public function isTypeUnique() : bool
    {
        return $this->getType() === self::TYPE_UNIQUE;
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

    /**
     * @return Collection|AttributeValue[]
     */
    public function getAvailableValues(): Collection
    {
        return $this->availableValues;
    }

    public function addAvailableValue(AttributeValue $availableValue): self
    {
        if (!$this->availableValues->contains($availableValue)) {
            $this->availableValues[] = $availableValue;
            $availableValue->setAttribute($this);
        }

        return $this;
    }

    public function removeAvailableValue(AttributeValue $availableValue): self
    {
        if ($this->availableValues->contains($availableValue)) {
            $this->availableValues->removeElement($availableValue);
            // set the owning side to null (unless already changed)
            if ($availableValue->getAttribute() === $this) {
                $availableValue->setAttribute(null);
            }
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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return Collection|MainValue[]
     */
    public function getMainValues(): Collection
    {
        return $this->mainValues;
    }

    public function addMainValue(MainValue $mainValue): self
    {
        if (!$this->mainValues->contains($mainValue)) {
            $this->mainValues[] = $mainValue;
            $mainValue->setAttribute($this);
        }

        return $this;
    }

    public function removeMainValue(MainValue $mainValue): self
    {
        if ($this->mainValues->removeElement($mainValue)) {
            // set the owning side to null (unless already changed)
            if ($mainValue->getAttribute() === $this) {
                $mainValue->setAttribute(null);
            }
        }

        return $this;
    }

}
