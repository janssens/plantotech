<?php

namespace App\Entity;

use App\Repository\PlantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlantRepository::class)
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
     * @ORM\OneToMany(targetEntity=PlantsPorts::class, mappedBy="plant", orphanRemoval=true)
     */
    private $ports;

    /**
     * @ORM\ManyToMany(targetEntity=Soil::class, mappedBy="plants")
     */
    private $soils;

    /**
     * @ORM\ManyToMany(targetEntity=Ph::class, mappedBy="plants")
     */
    private $phs;

    /**
     * @ORM\ManyToMany(targetEntity=Nutrient::class, mappedBy="plants")
     */
    private $nutrients;

    /**
     * @ORM\ManyToMany(targetEntity=Clay::class, mappedBy="plants")
     */
    private $clays;

    /**
     * @ORM\ManyToMany(targetEntity=Humus::class, mappedBy="plants")
     */
    private $humuses;

    /**
     * @ORM\ManyToMany(targetEntity=Humidity::class, mappedBy="plants")
     */
    private $humidities;

    /**
     * @ORM\OneToMany(targetEntity=FloweringAndCrop::class, mappedBy="plants", orphanRemoval=true)
     */
    private $floweringAndCrops;

    /**
     * @ORM\OneToMany(targetEntity=PlantsInsolations::class, mappedBy="plant", orphanRemoval=true)
     */
    private $insolations;

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
    private $attributes;

    public function __construct()
    {
        $this->sources = new ArrayCollection();
        $this->ports = new ArrayCollection();
        $this->soils = new ArrayCollection();
        $this->phs = new ArrayCollection();
        $this->nutrients = new ArrayCollection();
        $this->clays = new ArrayCollection();
        $this->humuses = new ArrayCollection();
        $this->humidities = new ArrayCollection();
        $this->floweringAndCrops = new ArrayCollection();
        $this->insolations = new ArrayCollection();
        $this->associations1 = new ArrayCollection();
        $this->associations2 = new ArrayCollection();
        $this->attributes = new ArrayCollection();
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
     * @return Collection|PlantsPorts[]
     */
    public function getPorts(): Collection
    {
        return $this->ports;
    }

    public function getNaturalPorts(): Collection
    {
        return $this->getPortsByType(PlantsPorts::PLANT_PORT_TYPE_NATURAL);

    }

    public function getPossiblePorts(): Collection
    {
        return $this->getPortsByType(PlantsPorts::PLANT_PORT_TYPE_POSSIBLE);
    }

    public function getPortsByType(int $type): Collection
    {
        return $this->getPorts()->filter(function (PlantsPorts $pp) use ($type) {
            return ($pp->getType() == $type);
        });
    }

    public function addPort(PlantsPorts $port): self
    {
        if (!$this->ports->contains($port)) {
            $this->ports[] = $port;
            $port->setPlant($this);
        }

        return $this;
    }

    public function removePort(PlantsPorts $port): self
    {
        if ($this->ports->contains($port)) {
            $this->ports->removeElement($port);
            // set the owning side to null (unless already changed)
            if ($port->getPlant() === $this) {
                $port->setPlant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Soil[]
     */
    public function getSoils(): Collection
    {
        return $this->soils;
    }

    public function addSoil(Soil $soil): self
    {
        if (!$this->soils->contains($soil)) {
            $this->soils[] = $soil;
            $soil->addPlant($this);
        }

        return $this;
    }

    public function removeSoil(Soil $soil): self
    {
        if ($this->soils->contains($soil)) {
            $this->soils->removeElement($soil);
            $soil->removePlant($this);
        }

        return $this;
    }

    /**
     * @return Collection|Ph[]
     */
    public function getPhs(): Collection
    {
        return $this->phs;
    }

    public function addPh(Ph $ph): self
    {
        if (!$this->phs->contains($ph)) {
            $this->phs[] = $ph;
            $ph->addPlant($this);
        }

        return $this;
    }

    public function removePh(Ph $ph): self
    {
        if ($this->phs->contains($ph)) {
            $this->phs->removeElement($ph);
            $ph->removePlant($this);
        }

        return $this;
    }

    /**
     * @return Collection|Nutrient[]
     */
    public function getNutrients(): Collection
    {
        return $this->nutrients;
    }

    public function addNutrient(Nutrient $nutrient): self
    {
        if (!$this->nutrients->contains($nutrient)) {
            $this->nutrients[] = $nutrient;
            $nutrient->addPlant($this);
        }

        return $this;
    }

    public function removeNutrient(Nutrient $nutrient): self
    {
        if ($this->nutrients->contains($nutrient)) {
            $this->nutrients->removeElement($nutrient);
            $nutrient->removePlant($this);
        }

        return $this;
    }

    /**
     * @return Collection|Clay[]
     */
    public function getClays(): Collection
    {
        return $this->clays;
    }

    public function addClay(Clay $clay): self
    {
        if (!$this->clays->contains($clay)) {
            $this->clays[] = $clay;
            $clay->addPlant($this);
        }

        return $this;
    }

    public function removeClay(Clay $clay): self
    {
        if ($this->clays->contains($clay)) {
            $this->clays->removeElement($clay);
            $clay->removePlant($this);
        }

        return $this;
    }

    /**
     * @return Collection|Humus[]
     */
    public function getHumuses(): Collection
    {
        return $this->humuses;
    }

    public function addHumus(Humus $humus): self
    {
        if (!$this->humuses->contains($humus)) {
            $this->humuses[] = $humus;
            $humus->addPlant($this);
        }

        return $this;
    }

    public function removeHumus(Humus $humus): self
    {
        if ($this->humuses->contains($humus)) {
            $this->humuses->removeElement($humus);
            $humus->removePlant($this);
        }

        return $this;
    }

    /**
     * @return Collection|Humidity[]
     */
    public function getHumidities(): Collection
    {
        return $this->humidities;
    }

    public function addHumidity(Humidity $humidity): self
    {
        if (!$this->humidities->contains($humidity)) {
            $this->humidities[] = $humidity;
            $humidity->addPlant($this);
        }

        return $this;
    }

    public function removeHumidity(Humidity $humidity): self
    {
        if ($this->humidities->contains($humidity)) {
            $this->humidities->removeElement($humidity);
            $humidity->removePlant($this);
        }

        return $this;
    }

    /**
     * @return Collection|FloweringAndCrop[]
     */
    public function getFloweringAndCrops(): Collection
    {
        return $this->floweringAndCrops;
    }

    public function addFloweringAndCrop(FloweringAndCrop $floweringAndCrop): self
    {
        if (!$this->floweringAndCrops->contains($floweringAndCrop)) {
            $this->floweringAndCrops[] = $floweringAndCrop;
            $floweringAndCrop->setPlants($this);
        }

        return $this;
    }

    public function removeFloweringAndCrop(FloweringAndCrop $floweringAndCrop): self
    {
        if ($this->floweringAndCrops->contains($floweringAndCrop)) {
            $this->floweringAndCrops->removeElement($floweringAndCrop);
            // set the owning side to null (unless already changed)
            if ($floweringAndCrop->getPlants() === $this) {
                $floweringAndCrop->setPlants(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PlantsInsolations[]
     */
    public function getInsolations(): Collection
    {
        return $this->insolations;
    }

    public function getInsolationsByIdeal(bool $ideal): Collection
    {
        return $this->getInsolations()->filter(function (PlantsInsolations $pi) use ($ideal) {
            return ($pi->getIdeal() == $ideal);
        });
    }

    public function addInsolation(PlantsInsolations $insolation): self
    {
        if (!$this->insolations->contains($insolation)) {
            $this->insolations[] = $insolation;
            $insolation->setPlant($this);
        }

        return $this;
    }

    public function removeInsolation(PlantsInsolations $insolation): self
    {
        if ($this->insolations->contains($insolation)) {
            $this->insolations->removeElement($insolation);
            // set the owning side to null (unless already changed)
            if ($insolation->getPlant() === $this) {
                $insolation->setPlant(null);
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
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    public function addAttribute(AttributeValue $attributeValue): self
    {
        if (!$this->attributes->contains($attributeValue)) {
            $this->attributes[] = $attributeValue;
            $attributeValue->addPlant($this);
        }

        return $this;
    }

    public function removeAttribute(AttributeValue $attributeValue): self
    {
        if ($this->attributes->contains($attributeValue)) {
            $this->attributes->removeElement($attributeValue);
            $attributeValue->removePlant($this);
        }

        return $this;
    }

}
