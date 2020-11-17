<?php

namespace App\Entity;

use App\Repository\PropertyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PropertyRepository::class)
 */
class Property
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
    private $code;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $availableValues = [];

    /**
     * @ORM\ManyToOne(targetEntity=AttributeFamily::class, inversedBy="properties")
     */
    private $family;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getName(): ?string
    {
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

    public function getAvailableValues(): ?array
    {
        return $this->availableValues;
    }

    public function setAvailableValues(?array $availableValues): self
    {
        $this->availableValues = $availableValues;

        return $this;
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
}
