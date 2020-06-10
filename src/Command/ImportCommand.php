<?php
// src/Command/ImportCommand.php
namespace App\Command;

use App\Entity\Clay;
use App\Entity\Humus;
use App\Entity\Nutrient;
use App\Entity\Ph;
use App\Entity\Plant;
use App\Entity\PlantFamily;
use App\Entity\PlantsPorts;
use App\Entity\Port;
use App\Entity\Soil;
use App\Entity\Source;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends Command
{
// the name of the command (the part after "bin/console")
protected static $defaultName = 'app:import-from-db';

    // simple key value
    const PLANT_KEY_LATIN_NAME = "nom_latin";
    const PLANT_KEY_NAME = "nom_commun";
    const PLANT_KEY_RUSTICITY = "rusticité";
    const PLANT_KEY_RUSTICITY_COMMENT = "rusticité_remarque";
    const PLANT_KEY_TEMPERATURE = "température_semi_persistance";
    const PLANT_KEY_NATIVE_PLACE = "milieu_origine";
    const PLANT_KEY_BOTANY_LEAF = "botanique_feuille";
    const PLANT_KEY_BOTANY_BRANCH = "botanique_branche";
    const PLANT_KEY_BOTANY_ROOT = "botanique_racine";
    const PLANT_KEY_BOTANY_FLOWER = "botanique_fleur";
    const PLANT_KEY_BOTANY_FRUIT = "botanique_fruit";
    const PLANT_KEY_BOTANY_SEED = "botanique_graine";
    const PLANT_KEY_DENSITY = "densité_m2";
    const PLANT_KEY_INTEREST = "cultivar_d_intérêt";
    const PLANT_KEY_SPECIFICITY = "spécificité";
    const PLANT_KEY_DISEASES_AND_PEST = "maladies_ravageurs";
    const PLANT_KEY_AUTHOR = "auteur";
    const PLANT_KEY_STRATUM = "strate";

    //ENUM
    const PLANT_KEY_SUCKER = "tendance_drageonnante";
    const PLANT_KEY_LIMESTONE = "exigence_calcaire";
    const PLANT_KEY_ROOT = "tendance_racinaire";
    const PLANT_KEY_LIFE_CYCLE = "cycle_vie";
    const PLANT_KEY_WOODY = "ligneux";
    const PLANT_KEY_PRIORITY = "priorité";
    const PLANT_KEY_DROUGHT_TOLERANCE = "résistance_sècheresse";
    const PLANT_KEY_LEAF_DENSITY = "feuillage_densité";
    const PLANT_KEY_FOLIAGE = "feuillage_persistance";

    //simple field to double fields
    const PLANT_KEY_HEIGHT = "hauteur_adulte";
    const PLANT_KEY_WIDTH = "largeur_adulte";
    const PLANT_KEY_SEXUAL_MATURITY = "maturité_sexuelle";

    //field to entity
    const PLANT_KEY_FAMILY = "famille";
    const PLANT_KEY_SOURCES = "source_information";

    const PLANTS_PORTS_KEY_TYPE = "naturel_ou_possible";
    const PLANTE_ACCUMULATRICE_NUTRIMENTS_KEY_NUTRIMENT = "nutriment";

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Load data from db.')
            ->setHelp('This command populate data from origin database')
            ->addArgument('host',  InputArgument::REQUIRED, 'hostname')
            ->addArgument('username',  InputArgument::REQUIRED, 'username')
            ->addArgument('database',  InputArgument::REQUIRED, 'database')
            ->addArgument('password',  InputArgument::REQUIRED, 'password')
            ->addOption('port','p', InputOption::VALUE_OPTIONAL, 'port',3306);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $port = $input->getOption('port');
        $host = $input->getArgument('host');
        $database = $input->getArgument('database');
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        $clean_db = new \MysqliDb($host, $username, $password, $database,$port);
        $db = clone $clean_db;
        $plants = $db->get('PLANTES');

        $map = array (
            'LIFE_CYCLE_VIVACE'=> Plant::LIFE_CYCLE_PERENNIAL,
            'LIFE_CYCLE_BISANNUELLE'=> Plant::LIFE_CYCLE_BIENNIAL,
            'LIFE_CYCLE_ANNUEL'=> Plant::LIFE_CYCLE_ANNUAL,
            'ROOT_TRAÇANTES'=> Plant::ROOT_CREEPING,
            'ROOT_MIXTES'=> Plant::ROOT_MIXED,
            'ROOT_FASCICULÉES'=> Plant::ROOT_FASCICULE,
            'ROOT_BULBES'=> Plant::ROOT_BULB,
            'ROOT_PIVOTANTES'=> Plant::ROOT_TAPROOT,
            'ROOT_TUBERCULES'=> Plant::ROOT_TUBER,
            'SUCKER_DRAGEONNANTES'=> Plant::SUCKER_YES,
            'SUCKER_DRAGEONNANTES+'=> Plant::SUCKER_VERY,
            'LIMESTONE_TOLÉRANT'=> Plant::LIMESTONE_TOLERANT,
            'LIMESTONE_ATTENTION'=> Plant::LIMESTONE_WARNING,
            'LIMESTONE_INDIFFÉRENT' => Plant::LIMESTONE_INDIFFERENT,
            'LEAF_DENSITY_DENSE' => Plant::LEAF_DENSITY_DENSE,
            'LEAF_DENSITY_MOYEN' => Plant::LEAF_DENSITY_MEDIUM,
            'LEAF_DENSITY_LÉGER' => Plant::LEAF_DENSITY_LIGHT,
            'FOLIAGE_PERSISTANT' => Plant::FOLIAGE_PERSISTANT,
            'FOLIAGE_SEMI PERSISTANT' => Plant::FOLIAGE_SEMI_PERSISTANT,
            'FOLIAGE_CADUC' => Plant::FOLIAGE_DECIDUOUS,
            'DROUGHT_TOLERANCE_1' => Plant::DROUGHT_TOLERANCE_1,
            'DROUGHT_TOLERANCE_2' => Plant::DROUGHT_TOLERANCE_2,
            'DROUGHT_TOLERANCE_3' => Plant::DROUGHT_TOLERANCE_3,
            'DROUGHT_TOLERANCE_4' => Plant::DROUGHT_TOLERANCE_4,
            'DROUGHT_TOLERANCE_5' => Plant::DROUGHT_TOLERANCE_5,
            'PRIORITY_1' => Plant::PRIORITY_1,
            'PRIORITY_2' => Plant::PRIORITY_2,
            'STRATUM_MÉDIANE' => Plant::STRATUM_MEDIUM,
            'STRATUM_ARBRISSEAU' => Plant::STRATUM_SHRUB,
            'STRATUM_GRIMPANTE' => Plant::STRATUM_CLIMBING,
            'STRATUM_ARBRE' => Plant::STRATUM_TREE,
            'STRATUM_BASSE' => Plant::STRATUM_LOW,
            'STRATUM_CANOPÉE' => Plant::STRATUM_CANOPY,
        );

        $parse_int = array('temperature','rusticity');

        $simple_keys = array('latin_name','name','rusticity','rusticity_comment','temperature',
            'native_place','botany_leaf','botany_branch','botany_root','botany_flower','botany_seed','density',
            'interest','specificity','diseases_and_pest','author');
        $enum_keys = array('life_cycle','root','sucker','limestone','leaf_density','foliage','priority',
            'drought_tolerance','stratum');
        $simple_to_double_keys = array('height','width','sexual_maturity');


        //soil
        $types_of_soil = array(
            'sol_tres_drainant' => 'drain+',
            'sol_drainant' => 'drain',
            'sol_frais' => 'fresh',
            'sol_lourd' => 'heavy'
        );
        $soils_map = array();
        foreach ($types_of_soil as $key => $type_name){
            $soil = $this->entityManager->getRepository(Soil::class)->findOneBy(array('name' => $type_name));
            if (!$soil){
                $soil = new Soil();
                $soil->setName($type_name);
                $this->entityManager->persist($soil);
                $this->entityManager->flush();
            }
            $soils_map[$key] = $soil;
        }

        //phs
        $types_of_ph = array(
            'acide' => 'acide',
            'neutre' => 'neutral',
            'basique' => 'base'
        );
        $ph_map = array();
        foreach ($types_of_ph as $key => $type_name){
            $ph = $this->entityManager->getRepository(Ph::class)->findOneBy(array('name' => $type_name));
            if (!$ph){
                $ph = new Ph();
                $ph->setName($type_name);
                $this->entityManager->persist($ph);
                $this->entityManager->flush();
            }
            $ph_map[$key] = $ph;
        }

        //nutrient
        $nutrients = array('Ca'=>'Calcium','Fe'=>'Fer','K'=>'Potassium','S'=>'Sulfur','Mg'=>'Magnesium');
        $nutrients_map = array();
        foreach ($nutrients as $symbol => $name){
            $nutrient = $this->entityManager->getRepository(Nutrient::class)->findOneBy(array('symbol' => $symbol));
            if (!$nutrient){
                $nutrient = new Nutrient();
                $nutrient->setSymbol($symbol);
                $nutrient->setName($name);
                $this->entityManager->persist($nutrient);
                $this->entityManager->flush();
            }
            $nutrients_map[$symbol] = $nutrient;
        }

        //clay
        $clays_map = array('cinq'=>Clay::SCALE_FIVE,'dix'=>Clay::SCALE_TEN,'vingt'=>Clay::SCALE_TWENTY,
            'trente'=>Clay::SCALE_THIRTY,'plus_de_trente'=>Clay::SCALE_MORE_THAN_THIRTY);

        //humus
        $humus_map = array('pauvre'=>'poor', 'correct'=>'correct', 'riche'=>'rich');
        foreach ($humus_map as $key => $name){
            $humus = $this->entityManager->getRepository(Humus::class)->findOneBy(array('quantity' => $name));
            if (!$humus){
                $humus = new Humus();
                $humus->setQuantity($name);
                $this->entityManager->persist($humus);
                $this->entityManager->flush();
            }
            $humus_map[$key] = $humus;
        }

        foreach ($plants as $plant) {

            $new_plant = new Plant();

            //key value part.
            foreach ($simple_keys as $key) {
                //$output->writeln($key. ':');
                $value = self::tolower($plant[constant('self::PLANT_KEY_' . strtoupper($key))]);
                $function = self::camelCase('set_' . $key);
                if (in_array($key, $parse_int)) {
                    preg_match('/\-*\d+/', $value, $matches);
                    if (count($matches)) {
                        $value = intval($matches[0] + 0);
                    } else {
                        $value = 0;
                    }
                }
                $new_plant->$function($value);
            }
            foreach ($enum_keys as $key) {
                $value = strtoupper($plant[constant('self::PLANT_KEY_' . strtoupper($key))]);
                if (strtoupper($value)) {
                    $local_value = $map[strtoupper($key) . '_' . strtoupper($value)];
                    $function = self::camelCase('set_' . $key);
                    $new_plant->$function($local_value);
                }
            }
            foreach ($simple_to_double_keys as $key) {
                $value = strtoupper($plant[constant('self::PLANT_KEY_' . strtoupper($key))]);
                preg_match('/(\d+)\D*(\d*)/', $value, $matches);
                if (count($matches) && $matches[0]) {
                    $min = intval($matches[1] + 0);
                    if ($matches[2]) {
                        $max = intval($matches[2] + 0);;
                    } else {
                        $max = $min;
                    }
                } else {
                    $min = $max = 0;
                }
                foreach (array('min', 'max') as $v) {
                    $k = $v . '_' . $key;
                    $function = self::camelCase('set_' . $v . '_' . $key);
                    $new_plant->$function(${$v});
                }
            }

            //family
            $family_name = self::tolower($plant[SELF::PLANT_KEY_FAMILY]);
            $family = $this->entityManager->getRepository(PlantFamily::class)->findOneBy(array('name' => $family_name));
            if (!$family) {
                $family = new PlantFamily();
                $family->setName($family_name);
                $this->entityManager->persist($family);
            }
            $new_plant->setFamily($family);

            //port
            $db = clone $clean_db;
            $db->where("nom_latin", $plant[SELF::PLANT_KEY_LATIN_NAME]);
            $plant_ports = $db->get("PLANTE_PORTS","*");
            foreach ($plant_ports as $plant_port) {
                if (!isset($plant_port['port'])){
                    var_dump($plant_port);die();
                }
                $port_name = self::tolower($plant_port['port']);
                $port = $this->entityManager->getRepository(Port::class)->findOneBy(array('name' => $port_name));
                if (!$port) {
                    $port = new Port();
                    $port->setName($port_name);
                    $this->entityManager->persist($port);
                }
                $type = ($plant_port[self::PLANTS_PORTS_KEY_TYPE] == 'naturel') ? PlantsPorts::PLANT_PORT_TYPE_NATURAL : PlantsPorts::PLANT_PORT_TYPE_POSSIBLE;
                $my_plant_port = $new_plant->getPortsByType($type);
                if (!$my_plant_port) {
                    $my_plant_port = new PlantsPorts();
                    $my_plant_port->setPlant($plant);
                    $my_plant_port->setType($type);
                }
                $my_plant_port->addPort($port);
                $this->entityManager->persist($my_plant_port);
            }

            // sources
            $re = '/((https*:\/\/(?:(?:(?!http).)*))|((?!http).)+)/mi';
            $str = $plant[self::PLANT_KEY_SOURCES];
            preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
            if (count($matches)){
                foreach ($matches as $match){
                    $new_source = new Source();
                    $new_source->setName(trim($match[0]));
                    $new_plant->addSource($new_source);
                    $this->entityManager->persist($new_source);
                }
            }

            //soil
            $db = clone $clean_db;
            $db->where("nom_latin", $plant[SELF::PLANT_KEY_LATIN_NAME]);
            $plant_soil = $db->getOne("PLANTE_SOLS","*");
            foreach ($soils_map as $key => $soil){
                if (isset($plant_soil[$key])) {
                    if ($plant_soil[$key] == 'OUI') {
                        $new_plant->addSoil($soil);
                    }
                }
            }

            //ph
            $db = clone $clean_db;
            $db->where("nom_latin", $plant[SELF::PLANT_KEY_LATIN_NAME]);
            $plant_ph = $db->getOne("PLANTE_PH","*");
            foreach ($ph_map as $key => $ph){
                if ($plant_ph[$key] == 'oui'){
                    $new_plant->addPh($ph);
                }
            }

            //nutrient
            $db = clone $clean_db;
            $db->where("nom_latin", $plant[SELF::PLANT_KEY_LATIN_NAME]);
            $plant_nutrients = $db->get("PLANTE_ACCUMULATRICE_NUTRIMENTS","*");
            foreach ($plant_nutrients as $plant_nutrient){
                $new_plant->addNutrient($nutrients_map[$plant_nutrient[self::PLANTE_ACCUMULATRICE_NUTRIMENTS_KEY_NUTRIMENT]]);
            }

            //clays
            $db = clone $clean_db;
            $db->where("nom_latin", $plant[SELF::PLANT_KEY_LATIN_NAME]);
            $plante_argile = $db->getOne("PLANTE_ARGILE","*");
            foreach ($clays_map as $key => $value){
                if (isset($plante_argile[$key])) {
                    if ($plante_argile[$key] == 'oui') {
                        $clay = new Clay();
                        $clay->addPlant($new_plant);
                        $clay->setScale($value);
                        $this->entityManager->persist($clay);
                    }
                }
            }

            //humus
            $db = clone $clean_db;
            $db->where("nom_latin", $plant[SELF::PLANT_KEY_LATIN_NAME]);
            $plante_humus = $db->getOne("PLANTE_HUMUS","*");
            foreach ($humus_map as $key => $humus){
                if (isset($plante_humus[$key])) {
                    if ($plante_humus[$key] == 'oui') {
                        $new_plant->addHumus($humus);
                    }
                }
            }

        }

        /**
        private $humidities;
        private $floweringAndCrops;
        private $insolations;
        private $associations;
        private $needs;
        private $interests;
         */

        return 1;

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

    public static function tolower($string) {
        return mb_strtolower($string);
    }

}
