<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[ORM\Table(name: 'image')]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private $id;

    #[ORM\Column(length:255)]
    private string $name;

    #[ORM\Column(length:255)]
    private ?string $origin;

    #[ORM\ManyToOne(targetEntity: Plant::class,inversedBy : "images")]
    #[ORM\JoinColumn(nullable:false)]
    private $plant;

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
        return $this->plant;
    }

    public function setPlant(?Plant $Plant): self
    {
        $this->plant = $Plant;

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

    static function grab_image($url, $save_to_dir)
    {
        $path      = parse_url($url, PHP_URL_PATH);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $new_filename = md5($url) . '_' . preg_replace(
            "/[^A-Za-z0-9]/",
            '',
            pathinfo($path, PATHINFO_FILENAME)
        );

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true, // important pour Wikimedia
            CURLOPT_USERAGENT => 'PlopcomBot/1.0 (https://plopcom.fr; contact@plopcom.fr)',
            CURLOPT_TIMEOUT => 20,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER => [
                'Accept: image/*,*/*;q=0.8'
            ],
        ]);

        $raw = curl_exec($ch);

        if ($raw === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception('cURL error: ' . $error);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception('HTTP error: ' . $httpCode. ' '. $url);
        }

        $save_to = $save_to_dir . '/' . $new_filename . '.' . $extension;

        if (file_exists($save_to)) {
            unlink($save_to);
        }

        file_put_contents($save_to, $raw);

        return $new_filename . '.' . $extension;
    }

    static function image_exists_locally(string $url, string $save_to_dir): bool
    {
        $path = parse_url($url, PHP_URL_PATH);
        if (!$path) {
            return false;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return false;
        }

        $filename = md5($url) . '_' . preg_replace(
            "/[^A-Za-z0-9]/",
            '',
            pathinfo($path, PATHINFO_FILENAME)
        );

        $filePath = rtrim($save_to_dir, '/') . '/' . $filename . '.' . $extension;

        if (!file_exists($filePath) || !is_file($filePath)) {
            return false;
        }

        $imageInfo = @getimagesize($filePath);
        if ($imageInfo === false) {
            return false;
        }

        return true;
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
