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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $min_width;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $max_width;

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
     * @ORM\Column(type="text", nullable=true)
     */
    private $interest;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $specificity;

    /**
     * todo: user real user or entity
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity=PlantFamily::class, inversedBy="plants")
     */
    private $family;

    /**
     * @ORM\OneToMany(targetEntity=Source::class, mappedBy="plant", orphanRemoval=true)
     */
    private $sources;

    /**
     * @ORM\ManyToMany(targetEntity=AttributeValue::class, mappedBy="plants")
     */
    private $attributes_values;

    private $_attributes_by_code;

    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="Plant", orphanRemoval=true,cascade={"persist"})
     */
    private $images;

    public function __construct()
    {
        $this->sources = new ArrayCollection();
        $this->attributes_values = new ArrayCollection();
        $this->_attributes_by_code = array();
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
        return self::makeSlug($this->getLatinName());
    }

    static function makeSlug($string,$length = 50){
        //Lower case everything
        $string = strtolower($string);
        //remove accent
        $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
        $string = strtr( $string, $unwanted_array );
        //Make alphanumeric (removes all other characters)
        $string = preg_replace("/[^a-z0-9+_\s-]/", "", $string);
        //Clean up multiple dashes or whitespaces
        $string = preg_replace("/[\s-]+/", " ", $string);
        //Convert whitespaces and underscore to dash
        $string = preg_replace("/[\s_]/", "-", $string);
        //shorten
        $string = substr($string,0,$length);
        return $string;
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

    public function getWoody(): ?bool
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

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): self
    {
        $this->author = $author;

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
     * @return Collection|AttributeValue[]
     */
    public function getAttributesValues(): Collection
    {
        return $this->attributes_values;
    }

    /**
     * @param string $code
     * @return ArrayCollection
     */
    public function getAttributeValuesByCode($code = ''){
        return $this->attributes_values->filter(function ($attribute_value) use ($code){
            /** @var AttributeValue $attribute_value */
            return $attribute_value->getAttribute()->getCode() == $code && !$attribute_value->getMainValue();
        });
    }

    /**
     * @param string $code
     * @return ArrayCollection
     */
    public function getMainAttributeValuesByCode($code = ''){
        return $this->attributes_values->filter(function ($attribute_value) use ($code){
            /** @var AttributeValue $attribute_value */
            /** @var Attribute $attribute */
            $attribute = $attribute_value->getAttribute();
            return $attribute->getCode() == $code && $attribute_value->getMainValue() && $attribute_value->getMainValue() === $attribute->getMainValue();
        });
    }

    public function getAttributeUniqueValueByCode($code = ''){
        if ($values = $this->getAttributeValuesByCode($code)){
            return $values->first();
        }
        return false;
    }

    public function getAttributeValuesByCodeAsArray($code = ''){
        $return = array();
        /** @var AttributeValue $attributeValue */
        foreach ( $this->getAttributeValuesByCode($code) as $attributeValue ){
            $return[$attributeValue->getCode()] = $attributeValue;
        }
        return $return;
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
