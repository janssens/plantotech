<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use App\Entity\Insolation;
use App\Entity\Plant;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('human', [$this, 'humanize']),
        ];
    }

    public function humanize($number, $type, $label = false)
    {
        $prefix = '';
        switch ($type){
            case 'life_cycle':
                if ($label) $prefix = 'plante : ';
                switch ($number){
                    case Plant::LIFE_CYCLE_ANNUAL:
                        return $prefix.'annuelle';
                    case Plant::LIFE_CYCLE_BIENNIAL:
                        return $prefix.'biannuelle';
                    case Plant::LIFE_CYCLE_PERENNIAL:
                        return $prefix.'vivace';
                };
            case 'root':
                if ($label) $prefix = 'racines : ';
                switch ($number){
                    case Plant::ROOT_CREEPING:
                        return $prefix.'traçantes';
                    case Plant::ROOT_FASCICULE:
                        return $prefix.'fasciculées';
                    case Plant::ROOT_MIXED:
                        return $prefix.'mixtes';
                    case Plant::ROOT_BULB:
                        return $prefix.'bulbes';
                    case Plant::ROOT_TAPROOT:
                        return $prefix.'pivot';
                    case Plant::ROOT_TUBER:
                        return $prefix.'tubercules';
                }
            case 'stratum':
                if ($label) $prefix = 'strate : ';
                switch ($number){
                    case Plant::STRATUM_LOW:
                        return $prefix.'basse';
                    case Plant::STRATUM_SHRUB:
                        return $prefix.'arbrisseau';
                    case Plant::STRATUM_MEDIUM:
                        return $prefix.'médiane';
                    case Plant::STRATUM_TREE:
                        return $prefix.'arbre';
                    case Plant::STRATUM_CANOPY:
                        return $prefix.'canopée';
                    case Plant::STRATUM_CLIMBING:
                        return $prefix.'grimpante';
                }
            case 'foliage':
                if ($label) $prefix = 'feuillage : ';
                switch ($number){
                    case Plant::FOLIAGE_PERSISTANT:
                        return $prefix.'persistant';
                    case Plant::FOLIAGE_SEMI_PERSISTANT:
                        return $prefix.'semi-persistant';
                    case Plant::FOLIAGE_DECIDUOUS:
                        return $prefix.'caduc';
                }
            case 'leaf_density':
                if ($label) $prefix = 'Densité du feuillage : ';
                switch ($number){
                    case Plant::LEAF_DENSITY_LIGHT:
                        return $prefix.'leger';
                    case Plant::LEAF_DENSITY_MEDIUM:
                        return $prefix.'moyen';
                    case Plant::LEAF_DENSITY_DENSE:
                        return $prefix.'dense';
                }
            case 'insolation':
                if ($label) $prefix = 'Exposition : ';
                switch ($number){
                    case Insolation::TYPE_SUN:
                        return $prefix.'soleil';
                    case Insolation::TYPE_HALF_SHADE:
                        return $prefix.'mi-ombre';
                    case Insolation::TYPE_SHADE:
                        return $prefix.'ombre';
                }
            case 'drought_tolerance':
                if ($label) $prefix = 'Tolérance à la sécheresse : ';
                switch ($number){
                    case Plant::DROUGHT_TOLERANCE_1:
                        return $prefix.'1/5';
                    case Plant::DROUGHT_TOLERANCE_2:
                        return $prefix.'2/5';
                    case Plant::DROUGHT_TOLERANCE_3:
                        return $prefix.'3/5';
                    case Plant::DROUGHT_TOLERANCE_4:
                        return $prefix.'4/5';
                    case Plant::DROUGHT_TOLERANCE_5:
                        return $prefix.'5/5';
                }
            default:
                return $number;
        }
    }
}
