<?php

namespace App\Service;

use App\Repository\ConfigRepository;

Class ConfigService{

    private $configRepository;

    public function __construct(ConfigRepository $configRepository){
        $this->configRepository= $configRepository;
    }

    public function getValue($path){
        $conf = $this->configRepository->getFromPath($path);
        if (is_object($conf)){
            return $conf->getValue();
        }
        return null;
    }
}