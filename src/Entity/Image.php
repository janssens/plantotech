<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 */
class Image
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Plant::class, inversedBy="images")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Plant;

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

    public function getPlant(): ?Plant
    {
        return $this->Plant;
    }

    public function setPlant(?Plant $Plant): self
    {
        $this->Plant = $Plant;

        return $this;
    }

    public function getSrc(): ?string
    {
        $src = self::getName();
        if (!$src){
            return '';
        }
        $re = '/^http(s?):\/\/(.*).(JPG|JPEG|PNG)/i';

        preg_match_all($re, $src, $matches, PREG_SET_ORDER, 0);

        if (!count($matches)){
            return '/uploads/'.$src;
        }
        return $src;
    }
}
