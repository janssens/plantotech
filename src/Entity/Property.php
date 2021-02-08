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

    public function getEditTemplates(string $type = 'line'){
        $filename = "property/_partial/".$this->getCode()."_".$type."_edit.html.twig";
        $default = "property/_partial/default_".$type."_edit.html.twig";
        return array($filename,$default);
    }

    public static function camelCase($string, $dontStrip = []){
        /*
         * This will take any dash or underscore turn it into a space, run ucwords against
         * it so it capitalizes the first letter in all words separated by a space then it
         * turns and deletes all spaces.
         */
        return lcfirst(str_replace('_', '', ucwords(preg_replace('/^a-z0-9'.implode('',$dontStrip).']+/', '_',$string))));
    }

}
