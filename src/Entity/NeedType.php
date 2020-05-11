<?php

namespace App\Entity;

use App\Repository\NeedTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NeedTypeRepository::class)
 */
class NeedType
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
     * @ORM\OneToMany(targetEntity=NeedValue::class, mappedBy="type", orphanRemoval=true)
     */
    private $needValues;

    public function __construct()
    {
        $this->needValues = new ArrayCollection();
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
     * @return Collection|NeedValue[]
     */
    public function getNeedValues(): Collection
    {
        return $this->needValues;
    }

    public function addNeedValue(NeedValue $needValue): self
    {
        if (!$this->needValues->contains($needValue)) {
            $this->needValues[] = $needValue;
            $needValue->setType($this);
        }

        return $this;
    }

    public function removeNeedValue(NeedValue $needValue): self
    {
        if ($this->needValues->contains($needValue)) {
            $this->needValues->removeElement($needValue);
            // set the owning side to null (unless already changed)
            if ($needValue->getType() === $this) {
                $needValue->setType(null);
            }
        }

        return $this;
    }
}
