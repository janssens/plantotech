<?php
// src/Command/ImportCommand.php
namespace App\Command;

use App\Entity\Attribute;
use App\Entity\AttributeValue;
use App\Entity\Clay;
use App\Entity\FloweringAndCrop;
use App\Entity\Humidity;
use App\Entity\Humus;
use App\Entity\Insolation;
use App\Entity\InterestType;
use App\Entity\Nutrient;
use App\Entity\Ph;
use App\Entity\Plant;
use App\Entity\PlantFamily;
use App\Entity\PlantsInsolations;
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

        $plant_map = array (
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

        //humidity
        $humidity_map = array('sec'=>Humidity::HUMIDITY_DRY, 'humide'=>Humidity::HUMIDITY_WET, 'immerge'=>Humidity::HUMIDITY_IMMERSED);

        //flowering_crop
        $flowering_crop_type_map = array('récolte'=>FloweringAndCrop::TYPE_CROP, 'floraison'=>FloweringAndCrop::TYPE_FLOWERING);
        $flowering_crop_month_map = array('janvier'=>1,'fevrier'=>2,'mars'=>3,'avril'=>4,'mai'=>5,'juin'=>6,'juillet'=>7,'aout'=>8,'septembre'=>9,'octobre'=>10,'novembre'=>11,'decembre'=>12);

        //insolation
        $insolation_ideal_map = array('tolérée'=>false,'idéale'=>true);
        $insolation_type_map = array('MI-OMBRE'=>array(Insolation::TYPE_HALF_SHADE),'SOLEIL'=>array(Insolation::TYPE_SUN),'SOLEIL & OMBRE'=>array(Insolation::TYPE_SUN,Insolation::TYPE_SHADE),'OMBRE'=>array(Insolation::TYPE_SHADE),'"MI-OMBRE'=>array(Insolation::TYPE_HALF_SHADE),'SOLEIL"'=>array(Insolation::TYPE_SUN));

        //Needs & Interest
        $needs_map = array(
            'besoin_brise_vent'=>'brise vent',
            'besoin_pollinisation'=>'pollinisation',
            'besoin_limitation_de_concurrence'=>'limitation de concurrence',
            'besoin_azote'=>'azote',
            'besoin_mineraux'=>'minéraux',
            'besoin_matiere_organique'=>'matière organique',
            'besoin_zonage'=>'zonage',
            'besoin_protection'=>'protection',
            'besoin_taille'=>'taille',
            'besoin_arrosage'=>'arrosage',
            'besoin_tuteur'=>'tuteur',
            'besoin_conduite'=>'conduite',
            'besoin_cultural_cueillette_ramassage'=>'mode de récolte',
            'besoin_cultural_recolte_etalee_groupee'=>'type de récolte',
            'besoin_cultural_precisions_recolte'=>'info récolte',
            'besoin_cultural_exigences_particulieres'=>'exigences',
            'besoin_cultural_entretien'=>'entretien',
            'sol_draine_compact_profond'=>'sol',
            );
        $interest_map = array(
            'interet_habitat_d_oiseaux' => 'habitat d\'oiseaux',
            'interet_insectes_auxilliaires' => 'insectes auxilliaires',
            'precisions_insectes_auxilliaires' => 'info insectes auxilliaires',
            'interet_mellifere' => 'mellifère',
            'interet_nectarifere' => 'nectarifére',
            'interet_pollen' => 'pollen',
            'interet_allelopathique' => 'allelopathique',
            'interet_couvre_sol' => 'couvre sol',
            'interet_fixateur_dazote' => 'fixateur d\'azote',
            'interet_fixateur_de_mineraux' => 'fixateur de minéraux',
            'precisions_fixateur_de_mineraux' => 'info fixateur de minéraux',
            'interet_ombre_legere' => 'ombre légère',
            'interet_anti_erosion' => 'anti érosion',
            'interet_biomasse' => 'biomasse',
            'interet_amelioration_du_compost' => 'amélioration du compost',
            'interet_amelioration_structure_du_sol' => 'amélioration de la structure du sol',
            'interet_pionnier' => 'pionnier',
            'interet_repulsif_insectes' => 'répulsif insectes',
            'fruit_comestible' => 'comestible',
            'fruit_de_table' => 'fruit de table',
            'fruit_grignotte' => 'grignote',
            'fruit_transforme' => 'transforme',
            'fruit_conserve_par_cuisson' => 'conservation par cuisson',
            'fruit_boisson' => 'boisson',
            'fruit_congelation' => 'congélation',
            'fruit_superfood' => 'super aliment',
            'fruit_de_garde' => 'fruit de garde',
            'graine_comestible' => 'graine comestible',
            'graine_grignotte' => 'graine grignote',
            'graine_cuite' => 'graine cuite',
            'graine_farine' => 'graine farine',
            'graine_sechee' => 'graine séchée',
            'graine_huile' => 'graine huile',
            'graine_germee' => 'graine germée',
            'petiole_feuille_comestible' => 'pétiole feuille comestible',
            'petiole_feuille_fraiche_seche_cuite' => 'petiole feuille fraiche séche cuite',
            'rhizome_tubercule_bulbe_comestible' => 'rhizome tubercule bulbe comestible',
            'fleur_comestible' => 'fleur comestible',
            'bourgeon_seve_comestible' => 'bourgeon sève comestible',
            'usage_medicinal' => 'usage médicinal',
            'proprietes_medicinales' => 'propriétés médicinales',
            'usage_aromatique' => 'usage aromatique',
            'fourrage_animal' => 'fourrage animal',
            'fourrage_grands_herbivores' => 'fourrage grands herbivores',
            'fourrage_basse_court' => 'fourrage basse court',
            'precision_fourrage' => 'précision fourrage',
            'bois_oeuvre' => 'bois d\'oeuvre',
            'tuteur' => 'tuteur',
            'vannerie' => 'vannerie',
            'bois_chauffage' => 'bois de chauffage',
            'porte_greffe' => 'porte greffe',
            'tinctoriale' => 'tinctorial',
            'ornementale' => 'ornementale',
            'odorante' => 'odorante',
            'interet_haie' => 'intérêt hair',
            'maladies_ravageurs' => 'maladie ravageurs',
            'multiplication' => 'multiplication',
            'associations_culinaires' => 'associations culinaires',
            'toxicite' => 'toxicité',);


        $output->writeln('ready to import');
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
                    $local_value = $plant_map[strtoupper($key) . '_' . strtoupper($value)];
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
            $output->writeln('==============');
            $output->writeln($new_plant->getName());
            $output->writeln('==============');

            $output->writeln('import family');
            //family
            $family_name = self::tolower($plant[SELF::PLANT_KEY_FAMILY]);
            $family = $this->entityManager->getRepository(PlantFamily::class)->findOneBy(array('name' => $family_name));
            if (!$family && $family_name) {
                $family = new PlantFamily();
                $family->setName($family_name);
                $this->entityManager->persist($family);
            }else if (!$family_name){
                $output->writeln('NO FAMILY');
            }
            $new_plant->setFamily($family);

            $output->writeln('import port');
            //port
            $db = clone $clean_db;
            $db->where("nom_latin", $plant[SELF::PLANT_KEY_LATIN_NAME]);
            $plant_ports = $db->get("PLANTE_PORTS");
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

                $my_plant_port = new PlantsPorts();
                $my_plant_port->setPlant($new_plant);
                $my_plant_port->setType($type);
                $my_plant_port->addPort($port);
                $this->entityManager->persist($my_plant_port);
            }

            $output->writeln('import sources');
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

            $output->writeln('import soil');
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

            $output->writeln('import ph');
            //ph
            $db = clone $clean_db;
            $db->where("nom_latin", $plant[SELF::PLANT_KEY_LATIN_NAME]);
            $plant_ph = $db->getOne("PLANTE_PH","*");
            foreach ($ph_map as $key => $ph){
                if ($plant_ph[$key] == 'oui'){
                    $new_plant->addPh($ph);
                }
            }

            $output->writeln('import nutrient');
            //nutrient
            $db = clone $clean_db;
            $db->where("nom_latin", $plant[SELF::PLANT_KEY_LATIN_NAME]);
            $plant_nutrients = $db->get("PLANTE_ACCUMULATRICE_NUTRIMENTS","*");
            foreach ($plant_nutrients as $plant_nutrient){
                $new_plant->addNutrient($nutrients_map[$plant_nutrient[self::PLANTE_ACCUMULATRICE_NUTRIMENTS_KEY_NUTRIMENT]]);
            }

            $output->writeln('import clays');
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

            $output->writeln('import humus');
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

            $output->writeln('import humidity');
            //humidity
            $db = clone $clean_db;
            $db->where("nom_latin", $plant[SELF::PLANT_KEY_LATIN_NAME]);
            $plante_humidity = $db->getOne("PLANTE_HUMIDITE","*");
            foreach ($humidity_map as $key => $value){
                if (isset($plante_humidity[$key])) {
                    if ($plante_humidity[$key] == 'oui') {
                        $humidity = new Humidity();
                        $humidity->addPlant($new_plant);
                        $humidity->setValue($value);
                        $this->entityManager->persist($humidity);
                    }
                }
            }

            $output->writeln('import flowering & crop');
            //Flowering & Crop
            $db = clone $clean_db;
            $db->where("nom_latin", $plant[SELF::PLANT_KEY_LATIN_NAME]);
            foreach ($flowering_crop_type_map as $key_type => $type_value){
                $db->where("floraison_ou_recolte", $key_type);
                $values = $db->getOne("PLANTE_FLORAISONS_RECOLTES","*");
                foreach ($flowering_crop_month_map as $month => $index){
                    if (isset($values[$month])) {
                        if (strlen($values[$month] ) > 0) {

                            $flowering_crop = new FloweringAndCrop();
                            $flowering_crop->setType($type_value);
                            $flowering_crop->setMonth($index);
                            $flowering_crop->setPlant($new_plant);
                            $this->entityManager->persist($flowering_crop);

                            $new_plant->addFloweringAndCrop($flowering_crop);
                        }
                    }
                }
            }

            $output->writeln('import insolation');
            //insolation
            $db = clone $clean_db;
            $db->where("nom_latin", $plant[SELF::PLANT_KEY_LATIN_NAME]);
            $insolations = $db->get("PLANTE_EXPOSITIONS");
            foreach ($insolations as $insolation){
                $types = array();
                $ideal = false;
                if (isset($insolation_type_map[$insolation['exposition']])){
                    $types = $insolation_type_map[$insolation['exposition']];
                }
                if (isset($insolation_ideal_map[$insolation['idéale_tolérée']])){
                    $ideal = $insolation_ideal_map[$insolation['idéale_tolérée']];
                }
                if (count($types) > 0){
                    foreach ($types as $type){
                        $i = $this->entityManager->getRepository(Insolation::class)
                            ->findOneBy(array('type'=>$type));
                        if (!$i){
                            $i = new Insolation();
                            $i->setType($type);
                            $this->entityManager->persist($i);
                        }
                        $pi = new PlantsInsolations();
                        $pi->setPlant($new_plant);
                        $pi->setIdeal($ideal);
                        $pi->setInsolation($i);
                        $this->entityManager->persist($pi);
                    }
                }
            }

            //association
            //corrupted data

            $output->writeln('import interest and needs');
            //interest and needs
            $db = clone $clean_db;
            $db->where("nom_latin", $plant[SELF::PLANT_KEY_LATIN_NAME]);
            $interests_and_needs = $db->getOne("PLANTE_BESOINS_INTERETS","*");
            foreach (array(Attribute::TYPE_INTEREST => $interest_map,Attribute::TYPE_NEED => $needs_map) as $type => $map){
                foreach ($map as $key => $value){

                    if (isset($interests_and_needs[$key])){
                        $att_value = strtolower(trim($interests_and_needs[$key]));
                        if (strlen($att_value) >= 1 ){
                            $attribute = $this->entityManager->getRepository(Attribute::class)
                                ->findOneBy(array('name'=>$value,'type'=>$type));
                            if (!$attribute){
                                $attribute = new Attribute();
                                $attribute->setName($value);
                                $attribute->setType($type);
                                $this->entityManager->persist($attribute);
                            }
                            $attribute_value = $this->entityManager->getRepository(AttributeValue::class)
                                ->findOneBy(array('value'=>$att_value,'attribute'=>$attribute));
                            if (!$attribute_value){
                                $attribute_value = new AttributeValue();
                                $attribute_value->setAttribute($attribute);
                                $attribute_value->setValue($att_value);
                            }
                            $attribute_value->addPlant($new_plant);
                            $this->entityManager->persist($attribute_value);
                        }
                    }
                }
            }
            $this->entityManager->persist($new_plant);
            $this->entityManager->flush();
        }

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
