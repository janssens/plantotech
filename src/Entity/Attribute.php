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
     * @ORM\OneToMany(targetEntity=AttributeValues::class, mappedBy="attribute", orphanRemoval=true)
     */
    private $availableValues;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $code;

    public function __construct()
    {
        $this->availableValues = new ArrayCollection();
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
     * @return Collection|AttributeValues[]
     */
    public function getAvailableValues(): Collection
    {
        return $this->availableValues;
    }

    public function addAvailableValue(AttributeValues $availableValue): self
    {
        if (!$this->availableValues->contains($availableValue)) {
            $this->availableValues[] = $availableValue;
            $availableValue->setAttribute($this);
        }

        return $this;
    }

    public function removeAvailableValue(AttributeValues $availableValue): self
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
}
