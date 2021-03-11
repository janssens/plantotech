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

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $origin;

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
        return '/uploads/'.$src;
    }

    static function urlOk($url){
        $re = '/(https?:\/\/.*\.wikimedia.org\/[^#]*\.jpg)/miU';
        preg_match($re, $url, $matches, PREG_OFFSET_CAPTURE, 0);
        if (count($matches)){
            return $matches[0][0];
        }
        else{
            return false;
        }
    }

    static function grab_image($url,$save_to_dir){

        $path      = parse_url($url, PHP_URL_PATH);       // get path from url
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION)); // get ext from path
        $new_filename  = md5($url).'_'.preg_replace("/[^A-Za-z0-9 ]/", '', pathinfo($path, PATHINFO_FILENAME));  // get name from path

        $ch = curl_init ($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $raw=curl_exec($ch);
        curl_close ($ch);
        $save_to = $save_to_dir.'/'.$new_filename.'.'.$extension;
        if(file_exists($save_to)){
            unlink($save_to);
        }
        $fp = fopen($save_to,'x');
        fwrite($fp, $raw);
        fclose($fp);

        return $new_filename.'.'.$extension;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(?string $origin): self
    {
        $this->origin = $origin;

        return $this;
    }
}
