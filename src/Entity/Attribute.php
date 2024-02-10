<?php

namespace App\Entity;

use App\Repository\AttributeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AttributeRepository::class)
 */
class Attribute extends PropertyOrAttribute
{
    const TYPE_NONE  = 1;
    const TYPE_SINGLE  = 2;
    const TYPE_MULTIPLE = 3;
    const TYPE_UNIQUE = 4;

    const ATTACHED_TO_SPECIES = 1;

    const ATTACHED_TO_VARIETY = 2;

    const ATTACHED_TO_BOTH = 3;

    /**
     * @ORM\Column(type="smallint")
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity=AttributeValue::class, mappedBy="attribute", orphanRemoval=true)
     */
    private $availableValues;

    /**
     * @ORM\OneToOne(targetEntity=MainValue::class, mappedBy="attribute", cascade={"persist", "remove"})
     */
    private $mainValue;

    /**
     * @ORM\Column(type="smallint")
     */
    private $attachedTo;

    public function __construct()
    {
        parent::__construct();
        $this->availableValues = new ArrayCollection();
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

    public function isTypeMultiple() : bool
    {
        return $this->getType() === self::TYPE_MULTIPLE;
    }

    /**
     * @return Collection|AttributeValue[]
     */
    public function getAvailableValues(): Collection
    {
        return $this->availableValues;
    }

    /**
     * @return Collection|AttributeValue[]
     */
    public function getAvailableMainValues(): Collection
    {
        return $this->availableValues->filter(function ($attribute_value) {
            return $attribute_value->getMainValue();
        });
    }

    /**
     * @return Collection|AttributeValue[]
     */
    public function getAvailableNonMainValues(): Collection
    {
        return $this->availableValues->filter(function ($attribute_value) {
            return !$attribute_value->getMainValue();
        });
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

    public function getMainValue(): ?MainValue
    {
        return $this->mainValue;
    }

    public function setMainValue(MainValue $mainValue): self
    {
        $this->mainValue = $mainValue;

        // set the owning side of the relation if necessary
        if ($mainValue->getAttribute() !== $this) {
            $mainValue->setAttribute($this);
        }

        return $this;
    }

    public function isAttribute(): bool
    {
        return true;
    }

    public function getTemplates(string $type = 'line'){
        $filename = "attribute/_partial/".$this->getCode()."_".$type.".html.twig";
        $default = "attribute/_partial/default_".$type.".html.twig";
        return array($filename,$default);
    }

    public function getAttachedTo(): ?int
    {
        return $this->attachedTo;
    }

    public function setAttachedTo(int $attachedTo): self
    {
        $this->attachedTo = $attachedTo;

        return $this;
    }

}
