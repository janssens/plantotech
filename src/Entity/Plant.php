<?php

namespace App\Entity;

use App\Repository\PlantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlantRepository::class)
 * @ORM\Table(name="plant", indexes={@ORM\Index(columns={"latin_name", "name"}, flags={"fulltext"})})
 */
class Plant
{

    const LIFE_CYCLE_UNDEFINED = 0;
    const LIFE_CYCLE_ANNUAL = 1;
    const LIFE_CYCLE_BIENNIAL = 2;
    const LIFE_CYCLE_PERENNIAL = 3;

    const ROOT_UNDEFINED = 0;
    const ROOT_CREEPING = 1;
    const ROOT_FASCICULE = 2;
    const ROOT_MIXED = 3;
    const ROOT_BULB = 4;
    const ROOT_TAPROOT = 5;
    const ROOT_TUBER = 6;

    const SUCKER_YES = 1;
    const SUCKER_VERY = 2;

    const LIMESTONE_TOLERANT = 1;
    const LIMESTONE_WARNING = 2;
    const LIMESTONE_INDIFFERENT = 4;

    const LEAF_DENSITY_LIGHT = 1;
    const LEAF_DENSITY_MEDIUM = 2;
    const LEAF_DENSITY_DENSE = 3;

    const FOLIAGE_PERSISTANT = 1;
    const FOLIAGE_SEMI_PERSISTANT = 2;
    const FOLIAGE_DECIDUOUS = 3;

    const PRIORITY_1 = 1;
    const PRIORITY_2 = 2;

    const DROUGHT_TOLERANCE_1 = 1;
    const DROUGHT_TOLERANCE_2 = 2;
    const DROUGHT_TOLERANCE_3 = 3;
    const DROUGHT_TOLERANCE_4 = 4;
    const DROUGHT_TOLERANCE_5 = 5;

    const STRATUM_UNDEFINED = 0;
    const STRATUM_LOW = 1;
    const STRATUM_SHRUB = 2;
    const STRATUM_MEDIUM = 3;
    const STRATUM_TREE = 4;
    const STRATUM_CANOPY = 5;
    const STRATUM_CLIMBING = 6;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $latin_name;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $life_cycle;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rusticity;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $rusticity_comment;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $temperature;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $woody;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $min_height;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $max_height;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $root;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $min_width;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $max_width;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $sucker;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $limestone;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $min_sexual_maturity;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $max_sexual_maturity;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $native_place;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $botany_leaf;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $botany_branch;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $botany_root;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $botany_flower;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $botany_fruit;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $botany_seed;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $density;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $leaf_density;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $foliage;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $interest;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $specificity;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $priority;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $drought_tolerance;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $diseases_and_pest;

    /**
     * todo: user real user or entity
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $author;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $stratum;

    /**
     * @ORM\ManyToOne(targetEntity=PlantFamily::class, inversedBy="plants")
     */
    private $family;

    /**
     * @ORM\OneToMany(targetEntity=Source::class, mappedBy="plant", orphanRemoval=true)
     */
    private $sources;

    /**
     * @ORM\OneToMany(targetEntity=Association::class, mappedBy="plant1", orphanRemoval=true)
     */
    private $associations1;

    /**
     * @ORM\OneToMany(targetEntity=Association::class, mappedBy="plant2", orphanRemoval=true)
     */
    private $associations2;

    private $associations = null;

    /**
     * @ORM\ManyToMany(targetEntity=AttributeValue::class, mappedBy="plants")
     */
    private $attributes_values;

    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="Plant", orphanRemoval=true,cascade={"persist"})
     */
    private $images;

    public function __construct()
    {
        $this->sources = new ArrayCollection();
        $this->associations1 = new ArrayCollection();
        $this->associations2 = new ArrayCollection();
        $this->attributes_values = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLatinName(): ?string
    {
        return $this->latin_name;
    }

    public function setLatinName(string $latin_name): self
    {
        $this->latin_name = $latin_name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): string
    {
        return strtolower(trim(preg_replace('/[\s-]+/', '-',
            preg_replace('/[^A-Za-z0-9-]+/', '-',
                preg_replace('/[&]/', 'and',
                    preg_replace('/[\']/', '',
                        iconv('UTF-8', 'ASCII//TRANSLIT', $this->getName()))))), '-'));
    }

    public function getLifeCycle(): ?int
    {
        return $this->life_cycle;
    }

    public function setLifeCycle(?int $life_cycle): self
    {
        if ($life_cycle>3 || $life_cycle<0){
            $life_cycle = self::LIFE_CYCLE_UNDEFINED;
        }
        $this->life_cycle = $life_cycle;

        return $this;
    }

    public function getRusticity(): ?int
    {
        return $this->rusticity;
    }

    public function setRusticity(?int $rusticity): self
    {
        $this->rusticity = $rusticity;

        return $this;
    }

    public function getRusticityComment(): ?string
    {
        return $this->rusticity_comment;
    }

    public function setRusticityComment(?string $rusticity_comment): self
    {
        $this->rusticity_comment = $rusticity_comment;

        return $this;
    }

    public function getTemperature(): ?int
    {
        return $this->temperature;
    }

    public function setTemperature(?int $temperature): self
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function getWoody(): ?int
    {
        return $this->woody;
    }

    public function setWoody(?int $woody): self
    {
        $this->woody = $woody;

        return $this;
    }

    public function getMinHeight(): ?int
    {
        return $this->min_height;
    }

    public function setMinHeight(?int $min_height): self
    {
        $this->min_height = $min_height;

        return $this;
    }

    public function getMaxHeight(): ?int
    {
        return $this->max_height;
    }

    public function setMaxHeight(?int $max_height): self
    {
        $this->max_height = $max_height;

        return $this;
    }

    public function getHeight(): string
    {
        if ($this->min_height == $this->max_height)
            return $this->min_height;
        return $this->min_height.'-'.$this->max_height;
    }

    public function getRoot(): ?int
    {
        return $this->root;
    }

    public function setRoot(?int $root): self
    {
        $this->root = $root;

        return $this;
    }

    public function getMinWidth(): ?int
    {
        return $this->min_width;
    }

    public function setMinWidth(int $min_width): self
    {
        $this->min_width = $min_width;

        return $this;
    }

    public function getMaxWidth(): ?int
    {
        return $this->max_width;
    }

    public function setMaxWidth(?int $max_width): self
    {
        $this->max_width = $max_width;

        return $this;
    }

    public function getWidth(): string
    {
        if ($this->min_width == $this->max_width)
            return $this->min_width;
        return $this->min_width.'-'.$this->max_width;
    }

    public function getSucker(): ?int
    {
        return $this->sucker;
    }

    public function setSucker(?int $sucker): self
    {
        $this->sucker = $sucker;

        return $this;
    }

    public function getLimestone(): ?int
    {
        return $this->limestone;
    }

    public function setLimestone(?int $limestone): self
    {
        $this->limestone = $limestone;

        return $this;
    }

    public function getMinSexualMaturity(): ?int
    {
        return $this->min_sexual_maturity;
    }

    public function setMinSexualMaturity(?int $min_sexual_maturity): self
    {
        $this->min_sexual_maturity = $min_sexual_maturity;

        return $this;
    }

    public function getMaxSexualMaturity(): ?int
    {
        return $this->max_sexual_maturity;
    }

    public function setMaxSexualMaturity(?int $max_sexual_maturity): self
    {
        $this->max_sexual_maturity = $max_sexual_maturity;

        return $this;
    }

    public function getNativePlace(): ?string
    {
        return $this->native_place;
    }

    public function setNativePlace(?string $native_place): self
    {
        $this->native_place = $native_place;

        return $this;
    }

    public function getBotanyLeaf(): ?string
    {
        return $this->botany_leaf;
    }

    public function setBotanyLeaf(?string $botany_leaf): self
    {
        $this->botany_leaf = $botany_leaf;

        return $this;
    }

    public function getBotanyBranch(): ?string
    {
        return $this->botany_branch;
    }

    public function setBotanyBranch(?string $botany_branch): self
    {
        $this->botany_branch = $botany_branch;

        return $this;
    }

    public function getBotanyRoot(): ?string
    {
        return $this->botany_root;
    }

    public function setBotanyRoot(?string $botany_root): self
    {
        $this->botany_root = $botany_root;

        return $this;
    }

    public function getBotanyFlower(): ?string
    {
        return $this->botany_flower;
    }

    public function setBotanyFlower(?string $botany_flower): self
    {
        $this->botany_flower = $botany_flower;

        return $this;
    }

    public function getBotanyFruit(): ?string
    {
        return $this->botany_fruit;
    }

    public function setBotanyFruit(?string $botany_fruit): self
    {
        $this->botany_fruit = $botany_fruit;

        return $this;
    }

    public function getBotanySeed(): ?string
    {
        return $this->botany_seed;
    }

    public function setBotanySeed(?string $botany_seed): self
    {
        $this->botany_seed = $botany_seed;

        return $this;
    }

    public function getDensity(): ?string
    {
        return $this->density;
    }

    public function setDensity(?string $density): self
    {
        $this->density = $density;

        return $this;
    }

    public function getLeafDensity(): ?int
    {
        return $this->leaf_density;
    }

    public function setLeafDensity(?int $leaf_density): self
    {
        $this->leaf_density = $leaf_density;

        return $this;
    }

    public function getFoliage(): ?int
    {
        return $this->foliage;
    }

    public function setFoliage(?int $foliage): self
    {
        $this->foliage = $foliage;

        return $this;
    }

    public function getInterest(): ?string
    {
        return $this->interest;
    }

    public function setInterest(?string $interest): self
    {
        $this->interest = $interest;

        return $this;
    }

    public function getSpecificity(): ?string
    {
        return $this->specificity;
    }

    public function setSpecificity(?string $specificity): self
    {
        $this->specificity = $specificity;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getDroughtTolerance(): ?int
    {
        return $this->drought_tolerance;
    }

    public function setDroughtTolerance(?int $drought_tolerance): self
    {
        $this->drought_tolerance = $drought_tolerance;

        return $this;
    }

    public function getDiseasesAndPest(): ?string
    {
        return $this->diseases_and_pest;
    }

    public function setDiseasesAndPest(?string $diseases_and_pest): self
    {
        $this->diseases_and_pest = $diseases_and_pest;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getStratum(): ?int
    {
        return $this->stratum;
    }

    public function setStratum(?int $stratum): self
    {
        $this->stratum = $stratum;

        return $this;
    }

    public function getFamily(): ?PlantFamily
    {
        return $this->family;
    }

    public function setFamily(?PlantFamily $family): self
    {
        $this->family = $family;

        return $this;
    }

    /**
     * @return Collection|Source[]
     */
    public function getSources(): Collection
    {
        return $this->sources;
    }

    public function addSource(Source $source): self
    {
        if (!$this->sources->contains($source)) {
            $this->sources[] = $source;
            $source->setPlant($this);
        }

        return $this;
    }

    public function removeSource(Source $source): self
    {
        if ($this->sources->contains($source)) {
            $this->sources->removeElement($source);
            // set the owning side to null (unless already changed)
            if ($source->getPlant() === $this) {
                $source->setPlant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Association[]
     */
    public function getAssociations(): Collection
    {
        if ($this->associations === null){
            $this->associations = new ArrayCollection(
                array_merge(
                    $this->associations1->toArray(),
                    $this->associations2->toArray())
            );
        }
        return $this->associations;
    }

    /**
     * @return Collection|AttributeValue[]
     */
    public function getAttributesValues(): Collection
    {
        return $this->attributes_values;
    }

    public function getAttributeValuesByCode(string $code){
        return $this->attributes_values->filter(function ($attribute) use ($code){
            /** @var AttributeValue $attribute */
            return $attribute->getAttribute()->getCode() == $code;
        });
    }


    public function addAttributeValue(AttributeValue $attributeValue): self
    {
        if (!$this->attributes_values->contains($attributeValue)) {
            $this->attributes_values[] = $attributeValue;
            $attributeValue->addPlant($this);
        }

        return $this;
    }

    public function removeAttributeValue(AttributeValue $attributeValue): self
    {
        if ($this->attributes_values->contains($attributeValue)) {
            $this->attributes_values->removeElement($attributeValue);
            $attributeValue->removePlant($this);
        }

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setPlant($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getPlant() === $this) {
                $image->setPlant(null);
            }
        }

        return $this;
    }

}
