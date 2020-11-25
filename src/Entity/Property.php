<?php

namespace App\Entity;

use App\Repository\PropertyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PropertyRepository::class)
 */
class Property extends PropertyOrAttribute
{

    public function isProperty(): bool
    {
        return true;
    }

    public function getTemplates(string $type = 'line'){
        $filename = "property/_partial/".$this->getCode()."_".$type.".html.twig";
        $default = "property/_partial/default_".$type.".html.twig";
        return array($filename,$default);
    }
}
