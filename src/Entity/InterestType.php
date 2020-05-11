<?php

namespace App\Entity;

use App\Repository\InterestTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InterestTypeRepository::class)
 */
class InterestType
{
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
     * @ORM\OneToMany(targetEntity=InterestValue::class, mappedBy="type", orphanRemoval=true)
     */
    private $interestValues;

    public function __construct()
    {
        $this->interestValues = new ArrayCollection();
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
     * @return Collection|InterestValue[]
     */
    public function getInterestValues(): Collection
    {
        return $this->interestValues;
    }

    public function addInterestValue(InterestValue $interestValue): self
    {
        if (!$this->interestValues->contains($interestValue)) {
            $this->interestValues[] = $interestValue;
            $interestValue->setType($this);
        }

        return $this;
    }

    public function removeInterestValue(InterestValue $interestValue): self
    {
        if ($this->interestValues->contains($interestValue)) {
            $this->interestValues->removeElement($interestValue);
            // set the owning side to null (unless already changed)
            if ($interestValue->getType() === $this) {
                $interestValue->setType(null);
            }
        }

        return $this;
    }
}
