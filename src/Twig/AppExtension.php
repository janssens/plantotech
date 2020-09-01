<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use App\Entity\Insolation;
use App\Entity\Ph;
use App\Entity\Plant;
use App\Entity\PlantFamily;
use App\Entity\Port;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    private $entityManager;
    private $mois_fr;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->mois_fr = [1=>'janvier',2=>'février',3=>'mars',4=>'avril',5=>'mai',6=>'juin',7=>'juillet',8=>'aout',
            9=>'septembre',10=>'octobre',11=>'novembre',12=>'decembre'];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('human', [$this, 'humanize']),
            new TwigFilter('autolink', [$this, 'autolink']),
        ];
    }

    public function autolink($text)
    {
        $re = '/http(s{0,1}):\/\/([\S]+)/im';
        return preg_replace($re, '<a rel="nofollow" href="http${1}://${2}">${2}</a>', $text);
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
                }
                break;
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
                break;
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
                break;
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
                break;
            case 'limestone':
                if ($label) $prefix = 'exigence calcaire : ';
                switch ($number){
                    case Plant::LIMESTONE_TOLERANT:
                        return $prefix.'tolérant';
                    case Plant::LIMESTONE_INDIFFERENT:
                        return $prefix.'indifférent';
                    case Plant::LIMESTONE_WARNING:
                        return $prefix.'attention';
                }
                break;
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
                break;
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
                break;
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
                break;
            case 'humuses':
                if ($label) $prefix = 'Sol : ';
                switch ($number){
                    case 'poor':
                        return $prefix.'pauvre';
                    case 'correct':
                        return $prefix.'correcte';
                    case 'rich':
                        return $prefix.'riche';
                    default:
                        return '?';
                }
            case 'soils':
                if ($label) $prefix = 'Sol : ';
                switch ($number){
                    case 'drain+':
                        return $prefix.'drainé+';
                    case 'drain':
                        return $prefix.'drainé';
                    case 'fresh':
                        return $prefix.'frais';
                    case 'heavy':
                        return $prefix.'lourd';
                    default:
                        return '?';
                }
            case 'family':
                if ($label) $prefix = 'Famille : ';
                $family = $this->entityManager->getRepository(PlantFamily::class)->find($number);
                if ($family)
                    return $prefix.$family->getName();
                return $number;
            case 'port':
                if ($label) $prefix = 'Port : ';
                /** @var Port $port */
                $port = $this->entityManager->getRepository(Port::class)->find($number);
                if ($port)
                    return $prefix.$port->getName();
                return $number;
            case 'ph':
                if ($label) $prefix = 'Ph : ';
                /** @var Ph $ph */
                $ph = $this->entityManager->getRepository(Ph::class)->find($number);
                if ($ph)
                    return $prefix.$ph->getName();
                return $number;
            case 'crops':
                if ($label) $prefix = 'Recolte : ';
                return $prefix.$this->mois_fr[$number];
            case 'flowerings':
                if ($label) $prefix = 'Floraison : ';
                return $prefix.$this->mois_fr[$number];
            default:
                return $number;
        }
    }
}
