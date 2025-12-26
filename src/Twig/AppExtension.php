<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use App\Entity\Insolation;
use App\Entity\Ph;
use App\Entity\Plant;
use App\Entity\PlantFamily;
use App\Entity\Port;
use App\Entity\Property;
use App\Service\ConfigService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Mdi\Mdi;

class AppExtension extends AbstractExtension
{
    private $entityManager;
    private $configService;
    private $mois_fr;

    public function __construct(EntityManagerInterface $entityManager,ConfigService $configService)
    {
        $this->entityManager = $entityManager;
        $this->configService = $configService;
        $this->mois_fr = [1=>'janvier',2=>'février',3=>'mars',4=>'avril',5=>'mai',6=>'juin',7=>'juillet',8=>'aout',
            9=>'septembre',10=>'octobre',11=>'novembre',12=>'decembre'];
    }

    /**
     * @return ConfigService
     */
    private function getConfigService(): ConfigService
    {
        return $this->configService;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('human', [$this, 'humanize']),
            new TwigFilter('get', [$this, 'get']),
            new TwigFilter('autolink', [$this, 'autolink']),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('config',[$this,'config']),
            new TwigFunction('mdi', [Mdi::class, 'mdi'], ['is_safe' => ['html']])
        ];
    }

    public function autolink($text)
    {
        $re = '/http(s{0,1}):\/\/([\S]+)/im';
        return preg_replace($re, '<a rel="nofollow" target="_blank" href="http${1}://${2}">${2}</a>', $text);
    }

    public function get($object,$property){
        $function = self::camelCase('get_' . $property);
        return $object->$function();
    }

    /**
     * @param $number
     * @param $type
     * @param false $label
     * @return string
     */
    public function humanize($number, $type, $label = false)
    {
        $prefix = '';
        switch ($type){
            case 'family':
                if ($label) $prefix = 'Famille : ';
                $family = $this->entityManager->getRepository(PlantFamily::class)->find($number);
                if ($family)
                    return $prefix.$family->getName();
                return $number;
            case 'rusticity':
                if ($label) $prefix = 'Rusticité : ';
                return $prefix.$number.'°C';
            default:
                return $number;
        }
    }

    public function config($path)
    {
        return $this->getConfigService()->getValue($path);
    }

    public static function camelCase($str, array $noStrip = [])
    {
        // non-alpha and non-numeric characters become spaces
        $str = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $str);
        $str = trim($str);
        // uppercase the first character of each word
        $str = ucwords($str);
        $str = str_replace(" ", "", $str);
        $str = lcfirst($str);

        return $str;
    }
}
