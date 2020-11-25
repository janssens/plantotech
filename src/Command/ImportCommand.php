<?php
// src/Command/ImportCommand.php
namespace App\Command;

use App\Entity\Attribute;
use App\Entity\AttributeFamily;
use App\Entity\AttributeValue;
use App\Entity\FilterCategory;
use App\Entity\MainValue;
use App\Entity\Plant;
use App\Entity\PlantFamily;
use App\Entity\Property;
use App\Entity\Source;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
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

    const TYPE_CROP = 1;
    const TYPE_FLOWERING = 2;

    const INSOLATION_TYPE_SUN = 'sun';
    const INSOLATION_TYPE_HALF_SHADE = 'mi-ombre';
    const INSOLATION_TYPE_SHADE = 'ombre';

    private $entityManager;
    private $attribute;
    private $attribute_map;
    private $attribute_values;
    private $attribute_main_value;
    private $attribute_family;
    private $excluded_from_auto_import;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->attribute = array();
        $this->attribute_values = array();
        $this->attribute_main_value = array();
        $this->attribute_family = array();
        $this->attribute_map = $attribute_map = array(
            'besoin_brise_vent'=> array('name' => 'a protéger du vent','type' => Attribute::TYPE_NONE),
            'besoin_pollinisation'=> array('name' => 'pollinisation','type' => Attribute::TYPE_UNIQUE),
            'besoin_limitation_de_concurrence'=> array('name' => 'limiter la concurrence','type' => Attribute::TYPE_MULTIPLE),
            'besoin_azote'=> array('name' => 'besoin en azote','type' => Attribute::TYPE_NONE),
            'besoin_mineraux'=> array('name' => 'besoin en minéraux','type' => Attribute::TYPE_SINGLE),
            'besoin_matiere_organique'=> array('name' => 'Besoin en matière organique','type' => Attribute::TYPE_SINGLE),
            'besoin_zonage'=> array('name' => 'zonage','type' => Attribute::TYPE_MULTIPLE),
            'besoin_protection'=> array('name' => 'protection','type' => Attribute::TYPE_MULTIPLE),
            'besoin_taille'=> array('name' => 'taille','type' => Attribute::TYPE_MULTIPLE),
            'besoin_arrosage'=> array('name' => 'arrosage','type' => Attribute::TYPE_SINGLE),
            'besoin_tuteur'=> array('name' => 'besoin tuteur','type' => Attribute::TYPE_NONE),
            'besoin_conduite'=> array('name' => 'conduite','type' => Attribute::TYPE_MULTIPLE),
            'besoin_cultural_cueillette_ramassage'=> array('name' => 'mode de récolte','type' => Attribute::TYPE_SINGLE),
            'besoin_cultural_recolte_etalee_groupee'=> array('name' => 'type de récolte','type' => Attribute::TYPE_SINGLE),
            'besoin_cultural_precisions_recolte'=> array('name' => 'info récolte','type' => Attribute::TYPE_UNIQUE),
            'besoin_cultural_exigences_particulieres'=> array('name' => 'exigences','type' => Attribute::TYPE_UNIQUE),
            'besoin_cultural_entretien'=> array('name' => 'entretien','type' => Attribute::TYPE_UNIQUE),
            'sol_draine_compact_profond'=> array('name' => 'sol','type' => Attribute::TYPE_NONE),
            'interet_habitat_d_oiseaux' => array('name' =>  'habitat d\'oiseaux','type' => Attribute::TYPE_NONE),
            'interet_insectes_auxilliaires' => array('name' =>  'insectes auxilliaires','type' => Attribute::TYPE_NONE),
            'precisions_insectes_auxilliaires' => array('name' =>  'info insectes auxilliaires','type' => Attribute::TYPE_UNIQUE),
            'interet_mellifere' => array('name' =>  'mellifère','type' => Attribute::TYPE_SINGLE),
            'interet_nectarifere' => array('name' =>  'nectarifére','type' => Attribute::TYPE_SINGLE),
            'interet_pollen' => array('name' =>  'pollen','type' => Attribute::TYPE_SINGLE),
            'interet_allelopathique' => array('name' =>  'allelopathique','type' => Attribute::TYPE_MULTIPLE),
            'interet_couvre_sol' => array('name' =>  'couvre sol','type' => Attribute::TYPE_SINGLE),
            'interet_fixateur_dazote' => array('name' =>  'fixateur d\'azote','type' => Attribute::TYPE_NONE),
            'interet_fixateur_de_mineraux' => array('name' =>  'fixateur de minéraux','type' => Attribute::TYPE_SINGLE),
            'precisions_fixateur_de_mineraux' => array('name' =>  'info fixateur de minéraux','type' => Attribute::TYPE_UNIQUE),
            'interet_ombre_legere' => array('name' =>  'ombre légère','type' => Attribute::TYPE_NONE),
            'interet_anti_erosion' => array('name' =>  'anti érosion','type' => Attribute::TYPE_NONE),
            'interet_biomasse' => array('name' =>  'biomasse','type' => Attribute::TYPE_NONE),
            'interet_amelioration_du_compost' => array('name' =>  'amélioration du compost','type' => Attribute::TYPE_NONE),
            'interet_amelioration_structure_du_sol' => array('name' =>  'améliore la structure du sol','type' => Attribute::TYPE_NONE),
            'interet_pionnier' => array('name' =>  'pionnier','type' => Attribute::TYPE_NONE),
            'interet_repulsif_insectes' => array('name' =>  'répulsif insectes','type' => Attribute::TYPE_NONE),
            'interet_repulsif_insectes_info' => array('name' =>  'info répulsif insectes','type' => Attribute::TYPE_UNIQUE),
            'fruit_comestible' => array('name' =>  'fruit comestible','type' => Attribute::TYPE_MULTIPLE),
            'fruit_de_table' => array('name' =>  'fruit de table','type' => Attribute::TYPE_NONE),
            'fruit_grignotte' => array('name' =>  'grignote','type' => Attribute::TYPE_NONE),
            'fruit_transforme' => array('name' =>  'transforme','type' => Attribute::TYPE_NONE),
            'fruit_conserve_par_cuisson' => array('name' =>  'conservation par cuisson','type' => Attribute::TYPE_NONE),
            'fruit_boisson' => array('name' =>  'boisson','type' => Attribute::TYPE_NONE),
            'fruit_congelation' => array('name' =>  'congélation','type' => Attribute::TYPE_NONE),
            'fruit_superfood' => array('name' =>  'super aliment','type' => Attribute::TYPE_NONE),
            'fruit_de_garde' => array('name' =>  'fruit de garde','type' => Attribute::TYPE_NONE),
            'graine_comestible' => array('name' =>  'graine comestible','type' => Attribute::TYPE_MULTIPLE),
            'graine_grignotte' => array('name' =>  'graine grignote','type' => Attribute::TYPE_NONE),
            'graine_cuite' => array('name' =>  'graine cuite','type' => Attribute::TYPE_NONE),
            'graine_farine' => array('name' =>  'graine farine','type' => Attribute::TYPE_NONE),
            'graine_sechee' => array('name' =>  'graine séchée','type' => Attribute::TYPE_NONE),
            'graine_huile' => array('name' =>  'graine huile','type' => Attribute::TYPE_NONE),
            'graine_germee' => array('name' =>  'graine germée','type' => Attribute::TYPE_NONE),
            'petiole_feuille_comestible' => array('name' =>  'pétiole feuille comestible','type' => Attribute::TYPE_MULTIPLE),
            'petiole_feuille_fraiche_seche_cuite' => array('name' =>  'petiole feuille fraiche séche cuite','type' => Attribute::TYPE_NONE),
            'rhizome_tubercule_bulbe_comestible' => array('name' =>  'rhizome tubercule bulbe comestible','type' => Attribute::TYPE_SINGLE),
            'fleur_comestible' => array('name' =>  'fleur comestible','type' => Attribute::TYPE_NONE),
            'bourgeon_seve_comestible' => array('name' =>  'bourgeon sève comestible','type' => Attribute::TYPE_MULTIPLE),
            'usage_medicinal' => array('name' =>  'usage médicinal','type' => Attribute::TYPE_MULTIPLE),
            'proprietes_medicinales' => array('name' =>  'propriétés médicinales','type' => Attribute::TYPE_UNIQUE),
            'usage_aromatique' => array('name' =>  'usage aromatique','type' => Attribute::TYPE_MULTIPLE),
            'fourrage_animal' => array('name' =>  'fourrage animal','type' => Attribute::TYPE_NONE),
            'fourrage_grands_herbivores' => array('name' =>  'fourrage grands herbivores','type' => Attribute::TYPE_SINGLE),
            'fourrage_basse_court' => array('name' =>  'fourrage basse court','type' => Attribute::TYPE_SINGLE),
            'precision_fourrage' => array('name' =>  'précision fourrage','type' => Attribute::TYPE_UNIQUE),
            'bois_oeuvre' => array('name' =>  'bois d\'oeuvre','type' => Attribute::TYPE_NONE),
            'tuteur' => array('name' =>  'peut servir de tuteur','type' => Attribute::TYPE_NONE),
            'vannerie' => array('name' =>  'vannerie','type' => Attribute::TYPE_MULTIPLE),
            'bois_chauffage' => array('name' =>  'bois de chauffage','type' => Attribute::TYPE_NONE),
            'porte_greffe' => array('name' =>  'peut servir de porte greffe','type' => Attribute::TYPE_NONE),
            'tinctoriale' => array('name' =>  'tinctorial','type' => Attribute::TYPE_NONE),
            'ornementale' => array('name' =>  'ornementale','type' => Attribute::TYPE_NONE),
            'odorante' => array('name' =>  'odorante','type' => Attribute::TYPE_NONE),
            'interet_haie' => array('name' =>  'intérêt haie','type' => Attribute::TYPE_MULTIPLE),
            'maladies_ravageurs' => array('name' =>  'maladie ravageurs','type' => Attribute::TYPE_MULTIPLE),
            'multiplication' => array('name' =>  'multiplication','type' => Attribute::TYPE_MULTIPLE),
            'multiplication_info' => array('name' =>  'info multiplication','type' => Attribute::TYPE_UNIQUE),
            'associations_culinaires' => array('name' =>  'associations culinaires','type' => Attribute::TYPE_UNIQUE),
            'toxicite' => array('name' =>  'toxicité','type' => Attribute::TYPE_SINGLE),
            'toxicite_info' => array('name' =>  'info toxicité','type' => Attribute::TYPE_UNIQUE),
            'comestible' => array('name' =>  'comestible','type' => Attribute::TYPE_NONE),
            'soil' => array('name' =>  'aeration du sol','type' => Attribute::TYPE_MULTIPLE),
            'ph' => array('name' =>  'ph du sol','type' => Attribute::TYPE_MULTIPLE),
            'humus' => array('name' =>  'richesse du sol','type' => Attribute::TYPE_MULTIPLE),
            'argile' => array('name' =>  'argile','type' => Attribute::TYPE_MULTIPLE),
            'humidity' => array('name' =>  'humidité','type' => Attribute::TYPE_SINGLE),
            'nutrient' => array('name' =>  'nutriments','type' => Attribute::TYPE_MULTIPLE),
            'crop' => array('name' =>  'récolte','type' => Attribute::TYPE_MULTIPLE),
            'flowering' => array('name' =>  'floraison','type' => Attribute::TYPE_MULTIPLE),
            'insolation' => array('name' =>  'exposition possible','type' => Attribute::TYPE_MULTIPLE),
            'port' => array('name' =>  'port possible','type' => Attribute::TYPE_MULTIPLE),
            'stratum' => array('name' =>  'strate','type' => Attribute::TYPE_SINGLE),
            'root' => array('name' =>  'tendance racinaire','type' => Attribute::TYPE_SINGLE),
            'life_cycle' => array('name' =>  'cycle de vie','type' => Attribute::TYPE_SINGLE),
            'foliage' => array('name' =>  'feuillage','type' => Attribute::TYPE_SINGLE),
            'limestone' => array('name' =>  'calcaire','type' => Attribute::TYPE_SINGLE),
            'leaf_density' => array('name' =>  'densité de feuillage','type' => Attribute::TYPE_SINGLE),
            'sucker' => array('name' =>  'drageonnant','type' => Attribute::TYPE_SINGLE),
            'drought_tolerance' => array('name' =>  'résistance sècheresse','type' => Attribute::TYPE_SINGLE),
            'priority' => array('name' =>  'priorité','type' => Attribute::TYPE_SINGLE),
            'wind_stopper' => array('name' =>  'brise vent','type' => Attribute::TYPE_NONE),
        );
        $this->excluded_from_auto_import = array(
            'graine_comestible','graine_grignotte','graine_cuite','graine_farine','graine_sechee','graine_huile','graine_germee',
            'fruit_comestible','fruit_de_table','fruit_grignotte','fruit_transforme','fruit_conserve_par_cuisson','fruit_boisson',
            'fruit_congelation','fruit_superfood','fruit_de_garde',
            'multiplication','multiplication_info',
            'toxicite','toxicite_info',
            'petiole_feuille_comestible',
            'petiole_feuille_fraiche_seche_cuite',
            'sol_draine_compact_profond',
            'bourgeon_seve_comestible',
            'interet_haie',
            'besoin_zonage','besoin_protection','besoin_taille','besoin_conduite',
            'maladies_ravageurs',
            'usage_aromatique',
            'usage_medicinal',
            'comestible',
            'sol','ph','humus','argile',
            'humidity',
            'nutrient',
            'crop','flowering',
            'insolation','port',
            'stratum','root','life_cycle','foliage','limestone','leaf_density','sucker','drought_tolerance','priority'
        );
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
            ->addOption('port','p', InputOption::VALUE_OPTIONAL, 'port',3306)
            ->addOption('limit','l', InputOption::VALUE_OPTIONAL, 'limit',3306);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $port = $input->getOption('port');
        $host = $input->getArgument('host');
        $database = $input->getArgument('database');
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $limit = $input->getOption('limit');
        if (!$limit){
            $limit = 99999;
        }

        $clean_db = new \MysqliDb($host, $username, $password, $database,$port);
        $db = clone $clean_db;
        $plants = $db->get('PLANTES');

        ////
        $max = count($plants);
        $progressBar = new ProgressBar($output, min($limit,$max));
        $progressBar->start();
        $counter = 0;

        $plant_map = array ();

        $parse_int = array('temperature','rusticity');

        $simple_keys = array('latin_name','name','rusticity','rusticity_comment','temperature',
            'native_place','botany_leaf','botany_branch','botany_root','botany_flower','botany_seed','density',
            'interest','specificity','author');
        $enum_keys = array('life_cycle','root','sucker','limestone','leaf_density','foliage','priority', 'drought_tolerance','stratum');
        $simple_to_double_keys = array('height','width','sexual_maturity');

        //soil
        $types_of_soil = array(
            'sol_tres_drainant' => 'très drainant',
            'sol_drainant' => 'drainant',
            'sol_frais' => 'frais',
            'sol_lourd' => 'lourd'
        );
        $soil_attribute = $this->newAttribute('soil');
        $soils_map = array();
        foreach ($types_of_soil as $key => $type_name){
            $soils_map[$key] = $this->newValue($type_name,$soil_attribute);
        }

        //phs
        $types_of_ph = array(
            'acide' => 'acide',
            'neutre' => 'neutre',
            'basique' => 'basique'
        );
        $ph_attribute = $this->newAttribute('ph');
        $ph_map = array();
        foreach ($types_of_ph as $key => $type_name){
            $ph_map[$key] = $this->newValue($type_name,$ph_attribute);
        }

        //priority
        $types_of_priority = array(
            'PRIORITY_0' => array('code'=>'0','label'=>'&#48;'), //0
            'PRIORITY_1' => array('code'=>'1','label'=>'1'),
            'PRIORITY_2' => array('code'=>'2','label'=>'2'),
        );
        $priority_attribute = $this->newAttribute('priority');
        foreach ($types_of_priority as $key => $data){
            $plant_map[$key] = $this->newValue($data['label'],$priority_attribute,$data['code']);
        }

        //drought_tolerance
        $types_of_drought_tolerance = array(
            'DROUGHT_TOLERANCE_1' => array('code'=>'1','label'=>'1/5'),
            'DROUGHT_TOLERANCE_2' => array('code'=>'2','label'=>'2/5'),
            'DROUGHT_TOLERANCE_3' => array('code'=>'3','label'=>'3/5'),
            'DROUGHT_TOLERANCE_4' => array('code'=>'4','label'=>'4/5'),
            'DROUGHT_TOLERANCE_5' => array('code'=>'5','label'=>'5/5'),
        );
        $drought_tolerance_attribute = $this->newAttribute('drought_tolerance');
        foreach ($types_of_drought_tolerance as $key => $data){
            $plant_map[$key] = $this->newValue($data['label'],$drought_tolerance_attribute,$data['code']);
        }

        //sucker
        $types_of_sucker = array(
            'SUCKER_DRAGEONNANTES'=> array('code'=>'yes','label'=>'drageonnantes'),
            'SUCKER_DRAGEONNANTES+'=> array('code'=>'very','label'=>'drageonnantes+'),
        );
        $sucker_attribute = $this->newAttribute('sucker');
        foreach ($types_of_sucker as $key => $data){
            $plant_map[$key] = $this->newValue($data['label'],$sucker_attribute,$data['code']);
        }

        //leaf_density
        $types_of_leaf_density = array(
            'LEAF_DENSITY_DENSE' => array('code'=>'dense','label'=>'dense'),
            'LEAF_DENSITY_MOYEN' => array('code'=>'medium','label'=>'moyen'),
            'LEAF_DENSITY_LÉGER' => array('code'=>'light','label'=>'léger'),
        );
        $leaf_density_attribute = $this->newAttribute('leaf_density');
        foreach ($types_of_leaf_density as $key => $data){
            $plant_map[$key] = $this->newValue($data['label'],$leaf_density_attribute,$data['code']);
        }

        //foliage
        $types_of_foliage = array(
            'FOLIAGE_PERSISTANT' => array('code'=>'persistant','label'=>'persistant'),
            'FOLIAGE_SEMI PERSISTANT' => array('code'=>'semi_persistant','label'=>'semi persistant'),
            'FOLIAGE_CADUC' => array('code'=>'deciduous','label'=>'caduc'),
        );
        $foliage_attribute = $this->newAttribute('foliage');
        foreach ($types_of_foliage as $key => $data){
            $plant_map[$key] = $this->newValue($data['label'],$foliage_attribute,$data['code']);
        }

        //limestone
        $types_of_limestone = array(
            'LIMESTONE_TOLÉRANT'=> array('code'=>'tolerant','label'=>'tolérant'),
            'LIMESTONE_ATTENTION'=> array('code'=>'warning','label'=>'attention'),
            'LIMESTONE_INDIFFÉRENT' => array('code'=>'indifferent','label'=>'indifférent'),
        );
        $limestone_attribute = $this->newAttribute('limestone');
        foreach ($types_of_limestone as $key => $data){
            $plant_map[$key] = $this->newValue($data['label'],$limestone_attribute,$data['code']);
        }

        //root
        $types_of_root = array(
            'ROOT_TRAÇANTES'=> array('code'=>'creeping','label'=>'traçantes'),
            'ROOT_MIXTES'=> array('code'=>'mixed','label'=>'mixtes'),
            'ROOT_FASCICULÉES'=> array('code'=>'rascicule','label'=>'fasciculées'),
            'ROOT_BULBES'=> array('code'=>'bulb','label'=>'bulbes'),
            'ROOT_PIVOTANTES'=> array('code'=>'raproot','label'=>'pivotantes'),
            'ROOT_TUBERCULES'=> array('code'=>'tuber','label'=>'tubercules'),
        );
        $root_attribute = $this->newAttribute('root');
        foreach ($types_of_root as $key => $data){
            $plant_map[$key] = $this->newValue($data['label'],$root_attribute,$data['code']);
        }

        //life cycle
        $life_cycles = array(
            'LIFE_CYCLE_VIVACE'=> array('code'=>'perennial','label'=>'vivace'),
            'LIFE_CYCLE_BISANNUELLE'=> array('code'=>'biennial','label'=>'bisannuelle'),
            'LIFE_CYCLE_ANNUEL'=> array('code'=>'annual','label'=>'annuel'),
        );
        $life_cycle_attribute = $this->newAttribute('life_cycle');
        foreach ($life_cycles as $key => $data){
            $plant_map[$key] = $this->newValue($data['label'],$life_cycle_attribute,$data['code']);
        }

        //stratum
        $strates = array(
            'STRATUM_MÉDIANE'=>array('code'=>'medium','label'=>'médiane'),
            'STRATUM_ARBRISSEAU'=>array('code'=>'shrub','label'=>'arbrisseau'),
            'STRATUM_GRIMPANTE'=>array('code'=>'climbing','label'=>'grimpante'),
            'STRATUM_ARBRE'=>array('code'=>'tree','label'=>'arbre'),
            'STRATUM_BASSE'=>array('code'=>'low','label'=>'basse'),
            'STRATUM_CANOPÉE'=>array('code'=>'canopy','label'=>'canopée'),
            );
        $stratum_attribute = $this->newAttribute('stratum');
        foreach ($strates as $key => $data){
            $plant_map[$key] = $this->newValue($data['label'],$stratum_attribute,$data['code']);
        }

        //nutrient
        $nutrients = array('Ca'=>'Calcium','Fe'=>'Fer','K'=>'Potassium','S'=>'Sulfur','Mg'=>'Magnesium');
        $nutrients_attribute = $this->newAttribute('nutrient');
        $nutrients_map = array();
        foreach ($nutrients as $symbol => $name){
            $nutrients_map[$symbol] = $this->newValue($name,$nutrients_attribute,$symbol);
        }

        //clay
        $clay_attribute = $this->newAttribute('argile');
        $types_of_clay = array(
            'cinq'=>array('code'=>5,'label'=>'cinq'),
            'dix'=>array('code'=>10,'label'=>'dix'),
            'vingt'=>array('code'=>20,'label'=>'vingt'),
            'trente'=>array('code'=>30,'label'=>'trente'),
            'plus_de_trente'=>array('code'=>31,'label'=>'plus de trente'),);
        $clays_map = array();
        foreach ($types_of_clay as $key => $data){
            $clays_map[$key] = $this->newValue($data['label'],$clay_attribute,$data['code']);
        }

        //humus
        $types_of_humus = array('pauvre'=>'pauvre', 'correct'=>'correct', 'riche'=>'riche');
        $humus_attribute = $this->newAttribute('humus');
        $humus_map = array();
        foreach ($types_of_humus as $key => $type_name){
            $humus_map[$key] = $this->newValue($type_name,$humus_attribute);
        }

        //humidity
        $types_of_humidity = array('sec'=>array('code'=>1,'label'=>'sec'), 'humide'=>array('code'=>50,'label'=>'humide'), 'immerge'=>array('code'=>100,'label'=>'immergé'));
        $humidity_attribute = $this->newAttribute('humidity');
        $humidity_map = array();
        foreach ($types_of_humidity as $key => $data){
            $humidity_map[$key] = $this->newValue($data['label'],$humidity_attribute,$data['code']);
        }

        //flowering_crop
        $flowering_crop_type_map = array('récolte'=>self::TYPE_CROP, 'floraison'=>self::TYPE_FLOWERING);
        $flowering_crop_month_map = array('janvier'=>1,'fevrier'=>2,'mars'=>3,'avril'=>4,'mai'=>5,'juin'=>6,'juillet'=>7,'aout'=>8,'septembre'=>9,'octobre'=>10,'novembre'=>11,'decembre'=>12);
        $flowering_attribute = $this->newAttribute('flowering');;
        $crop_attribute = $this->newAttribute('crop');
        $flowering_values = array();
        $crop_values = array();
        foreach ($flowering_crop_month_map as $name=>$code){
            $crop_values[$code] = $this->newValue($name,$crop_attribute,$code);
            $flowering_values[$code] = $this->newValue($name,$flowering_attribute,$code);
        }

        //insolation
        $insolation_ideal_map = array('tolérée'=>false,'idéale'=>true);
        $insolation_attribute = $this->newAttribute('insolation');
        $insolation_main_value = $this->newMainValue($insolation_attribute,'exposition idéale');
        $insolation_map = array();
        $insolation_type_map = array(
            'MI-OMBRE'=>array(self::INSOLATION_TYPE_HALF_SHADE),
            '"MI-OMBRE'=>array(self::INSOLATION_TYPE_HALF_SHADE),
            'SOLEIL'=>array(self::INSOLATION_TYPE_SUN),
            'SOLEIL"'=>array(self::INSOLATION_TYPE_SUN ),
            'SOLEIL & OMBRE'=>array(self::INSOLATION_TYPE_SHADE,self::INSOLATION_TYPE_SUN),
            'OMBRE'=>array(self::INSOLATION_TYPE_SHADE),
            );

        foreach (array(self::INSOLATION_TYPE_SHADE => 'ombre',self::INSOLATION_TYPE_SUN => 'soleil',self::INSOLATION_TYPE_HALF_SHADE => 'mi-ombre') as $code => $name){
            $insolation_map[$code] = $this->newValue($name,$insolation_attribute,$code);
        }

        //port
        $port_attribute = $this->newAttribute('port');
        $port_main_value = $this->newMainValue($port_attribute,'port naturel');
        $port_map = array();
        $port_type_map = array(
            'ACAULE' => array('name'=>'acaule','code'=>'acaule'),
            'ARBUSTIF ARRONDI' => array('name'=>'arbustif arrondi','code'=>'arbustif_arrondi'),
            'ARBUSTIF ÉLANCÉ' => array('name'=>'arbustif élancé','code'=>'arbustif_elance'),
            'ARBUSTIF TREILLE' => array('name'=>'arbustif treille','code'=>'arbustif_treille'),
            'BUISSONNANT ARRONDI' => array('name'=>'buissonnant arrondi','code'=>'buissonnant_arrondi'),
            'BUISSONNANT ÉLANCÉ' => array('name'=>'buissonnant élancé','code'=>'buissonnant_elance'),
            'GRIMPANT' => array('name'=>'grimpant','code'=>'grimpant'),
            'TAPISSANT' => array('name'=>'tapissant','code'=>'tapissant'),
            'TIGE' => array('name'=>'tige','code'=>'tige'),
            'TOUFFE' => array('name'=>'touffe','code'=>'touffe'),
        );
        foreach ($port_type_map as $key => $data){
            $port_map[$key] = $this->newValue($data['name'],$port_attribute,$data['code']);
        }

        $multivalue_custom_attributes = array();
        //'graine_comestible','graine_grignotte','graine_cuite','graine_farine','graine_sechee','graine_huile','graine_germee',
        $multivalue_custom_attributes['graine_comestible'] = array();
        $graine_comestible_attribute = $this->newAttribute('graine_comestible');
        $multivalue_custom_attributes['graine_comestible']['values'] = array();
        foreach (array('graine_grignotte'=>'grignotte','graine_cuite'=>'cuite','graine_farine'=>'farine','graine_sechee'=>'sechée','graine_huile'=>'huile','graine_germee'=>'germée') as $code => $name){
            $multivalue_custom_attributes['graine_comestible']['values'][$code] = $this->newValue($name,$graine_comestible_attribute,$code);
        }
        $multivalue_custom_attributes['graine_comestible']['attr'] = $graine_comestible_attribute;
        //'fruit_comestible','fruit_de_table','fruit_grignotte','fruit_transforme','fruit_conserve_par_cuisson','fruit_boisson','fruit_congelation','fruit_superfood','fruit_de_garde'
        $multivalue_custom_attributes['fruit_comestible'] = array();
        $fruit_comestible_attribute = $this->newAttribute('fruit_comestible');
        $multivalue_custom_attributes['fruit_comestible']['values'] = array();
        foreach (array('fruit_de_table'=>'de table','fruit_grignotte'=>'grignotte','fruit_transforme'=>'transformé','fruit_conserve_par_cuisson'=>'conservé par cuisson','fruit_boisson'=>'boisson','fruit_congelation'=>'congélation','fruit_superfood'=>'superfood','fruit_de_garde'=>'de garde') as $code=>$name){
            $multivalue_custom_attributes['fruit_comestible']['values'][$code] = $this->newValue($name,$fruit_comestible_attribute,$code);
        }
        $multivalue_custom_attributes['fruit_comestible']['attr'] = $fruit_comestible_attribute;

        $info_attributes = array();
        //'multiplication','multiplication_info',
        $info_attributes['multiplication'] = array();
        $info_attributes['multiplication']['attr'] = $this->newAttribute('multiplication');
        $info_attributes['multiplication']['info'] = $this->newAttribute('multiplication_info');
        //'toxicite','toxicite_info',
        $info_attributes['toxicite'] = array();
        $info_attributes['toxicite']['attr'] = $this->newAttribute('toxicite');
        $info_attributes['toxicite']['info'] = $this->newAttribute('toxicite_info');

        //'petiole_feuille_comestible',48,'petiole_feuille_fraiche_seche_cuite',
        $petiole_feuille_comestible_attribute = $this->newAttribute('petiole_feuille_comestible');
        foreach (array('petiole fraiche','feuille fraiche','petiole sèche','feuille sèche','petiole cuite','feuille cuite') as $value){
            $this->newValue($value,$petiole_feuille_comestible_attribute);
        }
        //'sol_draine_compact_profond',20 ? redontant avec Soil ?
        //sol drainé
        //sol drainé/sol profond
        //sol drainésol compactsol profond
        //sol drainéet acide
        //sol profond
        //sol compact
        //'bourgeon_seve_comestible',61
        $bourgeon_seve_comestible_attribute = $this->newAttribute('bourgeon_seve_comestible');
        foreach (array('sève','bourgeon') as $value){
            $this->newValue($value,$bourgeon_seve_comestible_attribute);
        }
        //'interet_haie', 54
        $interet_haie_attribute = $this->newAttribute('interet_haie');
        foreach (array('champêtre','fruitière','défensive','brise-vue','brise-vent','nourricière') as $value){
            $this->newValue($value,$interet_haie_attribute);
        }
        //'besoin_zonage',16
        $besoin_zonage_attribute = $this->newAttribute('besoin_zonage');
        for ($i=1;$i<6;$i++){
            $this->newValue($i,$besoin_zonage_attribute);
        }
        //'maladies_ravageurs',58
        $maladies_ravageurs_attribute = $this->newAttribute('maladies_ravageurs');
        foreach (array('gel','pucerons','tavelure','carpocapse','chenilles','rouille','anthracnose','autres petits insectes') as $value){
            $this->newValue($value,$maladies_ravageurs_attribute);
        }
        //'besoin_protection',33
        $besoin_protection_attribute = $this->newAttribute('besoin_protection');
        foreach (array('gel','insectes','herbivores','mollusques') as $value){
            $this->newValue($value,$besoin_protection_attribute);
        }
        //'usage_medicinal',11
        $usage_medicinal_attribute = $this->newAttribute('usage_medicinal');
        foreach (array('feuille','fruit','graine','fleur','bourgeon','ecorce','chaton','jeune pousse','racine','tige','sève','bois','rhizome','plante entière') as $value){
            $this->newValue($value,$usage_medicinal_attribute);
        }
        //'besoin_taille',34
        $besoin_taille_attribute = $this->newAttribute('besoin_taille');
        foreach (array('fruitière','régénérative') as $value){
            $this->newValue($value,$besoin_taille_attribute);
        }
        //'besoin_conduite',37
        $besoin_conduite_attribute = $this->newAttribute('besoin_conduite');
        foreach (array('palissée','parasol','colonie','trogne','haie','isolé','colonie') as $value){
            $this->newValue($value,$besoin_conduite_attribute);
        }
        //'usage_aromatique',44
        $usage_aromatique_attribute = $this->newAttribute('usage_aromatique');
        foreach (array('feuille','fleur','graine','rhizome','bourgeon') as $value){
            $this->newValue($value,$usage_aromatique_attribute);
        }

        $wind_stopper = $this->newAttribute('wind_stopper');
        $this->newValue('',$wind_stopper);

        //the missing attribute
        $comestible = $this->newAttribute('comestible');
        $comestible_value = $this->newValue('',$comestible);

        $this->entityManager->flush();

        $output->writeln('ready to import',OutputInterface::VERBOSITY_VERBOSE);

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
                if ($value) {
                    $local_value = $plant_map[strtoupper($key) . '_' . strtoupper($value)];
                    if ($local_value)
                        $new_plant->addAttributeValue($local_value);
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
            $output->writeln('==============',OutputInterface::VERBOSITY_VERBOSE);
            $output->writeln($new_plant->getName(),OutputInterface::VERBOSITY_VERBOSE);
            $output->writeln('==============',OutputInterface::VERBOSITY_VERBOSE);

            $output->writeln('import family',OutputInterface::VERBOSITY_VERBOSE);
            //family
            $family_name = self::tolower($plant[self::PLANT_KEY_FAMILY]);
            $family = $this->entityManager->getRepository(PlantFamily::class)->findOneBy(array('name' => $family_name));
            if (!$family && $family_name) {
                $family = new PlantFamily();
                $family->setName($family_name);
                $this->entityManager->persist($family);
            }else if (!$family_name){
                $output->writeln('NO FAMILY',OutputInterface::VERBOSITY_VERBOSE);
            }
            $new_plant->setFamily($family);

            $output->writeln('import port',OutputInterface::VERBOSITY_VERBOSE);
            //port
            $db = clone $clean_db;
            $db->where("nom_latin", $plant[self::PLANT_KEY_LATIN_NAME]);
            $plant_ports = $db->get("PLANTE_PORTS");
            foreach ($plant_ports as $plant_port) {
                if (!isset($plant_port['port'])){
                    var_dump($plant_port);die();
                }
                $port_name = $plant_port['port'];
                if (isset($port_map[$port_name])){
                    $new_plant->addAttributeValue($port_map[$port_name]);
                    $natural = $plant_port[self::PLANTS_PORTS_KEY_TYPE] == 'naturel';
                    if ($natural){
                        /** @var AttributeValue $value */
                        $value = $port_map[$port_name];
                        $value->setMainValue($port_main_value);
                    }
                }
            }

            $output->writeln('import sources',OutputInterface::VERBOSITY_VERBOSE);
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

            $output->writeln('import soil',OutputInterface::VERBOSITY_VERBOSE);
            //soil
            $db = clone $clean_db;
            $db->where("nom_latin", $plant[self::PLANT_KEY_LATIN_NAME]);
            $plant_soil = $db->getOne("PLANTE_SOLS","*");
            foreach ($soils_map as $key => $soil){
                if (isset($plant_soil[$key])) {
                    if ($plant_soil[$key] == 'OUI') {
                        if (isset($soils_map[$key])) {
                            $new_plant->addAttributeValue($soils_map[$key]);
                        }
                    }
                }
            }

            $output->writeln('import ph',OutputInterface::VERBOSITY_VERBOSE);
            //ph
            $db = clone $clean_db;
            $db->where("nom_latin", $plant[self::PLANT_KEY_LATIN_NAME]);
            $plant_ph = $db->getOne("PLANTE_PH","*");
            foreach ($ph_map as $key => $ph){
                if ($plant_ph[$key] == 'oui'){
                    if (isset($ph_map[$key])) {
                        $new_plant->addAttributeValue($ph_map[$key]);
                    }
                }
            }

            $output->writeln('import humus',OutputInterface::VERBOSITY_VERBOSE);
            //humus
            $db = clone $clean_db;
            $db->where("nom_latin", $plant[self::PLANT_KEY_LATIN_NAME]);
            $plante_humus = $db->getOne("PLANTE_HUMUS","*");
            foreach ($humus_map as $key => $humus){
                if (isset($plante_humus[$key])) {
                    if ($plante_humus[$key] == 'oui') {
                        if (isset($humus_map[$key])) {
                            $new_plant->addAttributeValue($humus_map[$key]);
                        }
                    }
                }
            }

            $output->writeln('import nutrient',OutputInterface::VERBOSITY_VERBOSE);
            //nutrient
            $db = clone $clean_db;
            $db->where("nom_latin", $plant[self::PLANT_KEY_LATIN_NAME]);
            $plant_nutrients = $db->get("PLANTE_ACCUMULATRICE_NUTRIMENTS","*");
            foreach ($plant_nutrients as $plant_nutrient){
                $new_plant->addAttributeValue($nutrients_map[$plant_nutrient[self::PLANTE_ACCUMULATRICE_NUTRIMENTS_KEY_NUTRIMENT]]);
            }

            $output->writeln('import clays',OutputInterface::VERBOSITY_VERBOSE);
            //clays
            $db = clone $clean_db;
            $db->where("nom_latin", $plant[self::PLANT_KEY_LATIN_NAME]);
            $plante_argile = $db->getOne("PLANTE_ARGILE","*");
            foreach ($clays_map as $key => $value){
                if (isset($plante_argile[$key])) {
                    if ($plante_argile[$key] == 'oui') {
                        if (isset($clays_map[$key])) {
                            $new_plant->addAttributeValue($clays_map[$key]);
                        }
                    }
                }
            }



            $output->writeln('import humidity',OutputInterface::VERBOSITY_VERBOSE);
            //humidity
            $db = clone $clean_db;
            $db->where("nom_latin", $plant[self::PLANT_KEY_LATIN_NAME]);
            $plante_humidity = $db->getOne("PLANTE_HUMIDITE","*");
            foreach ($humidity_map as $key => $value){
                if (isset($plante_humidity[$key])) {
                    if ($plante_humidity[$key] == 'oui') {
                        $new_plant->addAttributeValue($humidity_map[$key]);
                    }
                }
            }

            $output->writeln('import flowering & crop',OutputInterface::VERBOSITY_VERBOSE);
            //Flowering & Crop
            foreach ($flowering_crop_type_map as $key_type => $type_value){
                $db = clone $clean_db;
                $db->where("nom_latin", $plant[self::PLANT_KEY_LATIN_NAME]);
                $db->where("floraison_ou_recolte", $key_type);
                $values = $db->getOne("PLANTE_FLORAISONS_RECOLTES","*");
                foreach ($flowering_crop_month_map as $month => $index){
                    if (isset($values[$month])) {
                        if (strlen($values[$month] ) > 0) {
                            switch ($type_value){
                                case self::TYPE_CROP:
                                    $new_plant->addAttributeValue($crop_values[$index]);
                                    break;
                                case self::TYPE_FLOWERING:
                                    $new_plant->addAttributeValue($flowering_values[$index]);
                                    break;
                                default:
                                    break;
                            }
                        }
                    }
                }
            }

            $output->writeln('import insolation',OutputInterface::VERBOSITY_VERBOSE);
            //insolation
            $db = clone $clean_db;
            $db->where("nom_latin", $plant[self::PLANT_KEY_LATIN_NAME]);
            $insolations = $db->get("PLANTE_EXPOSITIONS");
            foreach ($insolations as $insolation){
                /**
                 * [exposition] => MI-OMBRE
                 * [idéale_tolérée] => tolérée
                 */
                $types = array();
                $ideal = false;
                if (isset($insolation_type_map[$insolation['exposition']])){
                    $types = $insolation_type_map[$insolation['exposition']];
                }
                if (isset($insolation_ideal_map[$insolation['idéale_tolérée']])){
                    $ideal = $insolation_ideal_map[$insolation['idéale_tolérée']];
                }
                if ($ideal && count($types) > 1){
                    die('Many ideal isolations ?!'."\n");
                }
                foreach ($types as $type){
                    $new_plant->addAttributeValue($insolation_map[$type]);
                }
                if ($ideal){
                    /** @var AttributeValue $value */
                    $value = $insolation_map[$types[0]]; //only one type
                    $value->setMainValue($insolation_main_value);
                }
            }

            //association
            //corrupted data

            $output->writeln('import interest and needs',OutputInterface::VERBOSITY_VERBOSE);
            //interest and needs
            $db = clone $clean_db;
            $db->where("nom_latin", $plant[self::PLANT_KEY_LATIN_NAME]);
            $interests_and_needs = $db->getOne("PLANTE_BESOINS_INTERETS","*");
            foreach ($this->attribute_map as $key => $value){
                if (!in_array($key,$this->excluded_from_auto_import)){
                    $output->writeln('import attribute '.$key,OutputInterface::VERBOSITY_VERBOSE);
                    if (isset($interests_and_needs[$key])){
                        $att_value = strtolower(trim($interests_and_needs[$key]));
                        if (strlen($att_value) >= 1 ){
                            $attribute = $this->entityManager->getRepository(Attribute::class)
                                ->findOneBy(array('code'=>$key));
                            if (!$attribute){
                                $attribute = $this->newAttribute($key);
                            }
                            switch ($attribute->getType()){
                                case Attribute::TYPE_NONE :
                                    $attribute_value = $this->newValue('',$attribute);
                                    $new_plant->addAttributeValue($attribute_value);
                                    break;
                                case Attribute::TYPE_UNIQUE :
                                case Attribute::TYPE_MULTIPLE :
                                    $attribute_value = $this->newValue($att_value,$attribute);
                                    $new_plant->addAttributeValue($attribute_value);
                                    break;
                                case Attribute::TYPE_SINGLE :
                                    /** @var AttributeValue $attribute_value */
                                    $attribute_value = $this->newValue($att_value,$attribute);
                                    $new_plant->addAttributeValue($attribute_value);
                                    break;
                                default:
                            }
                        }
                    }
                }
            }
            foreach (array('usage_aromatique','besoin_conduite','besoin_taille','usage_medicinal','besoin_protection','maladies_ravageurs','interet_haie','bourgeon_seve_comestible') as $code ){
                $output->writeln('import attribute '.$code,OutputInterface::VERBOSITY_VERBOSE);
                /** @var Attribute $multivalue_custom_attr */
                $multivalue_custom_attr = $this->entityManager->getRepository(Attribute::class)->findOneBy(array('code'=>$code));
                if (isset($interests_and_needs[$code])&&$multivalue_custom_attr){
                    $att_value = strtolower(trim($interests_and_needs[$code]));
                    if (strlen($att_value) >= 1 ){
                        foreach ($multivalue_custom_attr->getAvailableValues() as $value){
                            if (strpos($att_value,$value->getValue())>0 || strpos($att_value,$value->getValue()) === 0){
                                $new_plant->addAttributeValue($value);
                            }
                        }
                    }
                }
            }
            $wind_stopper = $this->newAttribute('wind_stopper');
            $wind_stopper_value = $this->newValue('',$wind_stopper);
            $haie_wind_stopper_value = $this->entityManager->getRepository(AttributeValue::class)->findOneBy(array('code'=>'brise-vent'));
            if (isset($interests_and_needs['interet_brise_vent'])){
                $att_value = strtolower(trim($interests_and_needs['interet_brise_vent']));
                if (strlen($att_value) >= 1 ){
                    $new_plant->addAttributeValue($wind_stopper_value);
                    $new_plant->addAttributeValue($haie_wind_stopper_value);
                }
            }
            //'besoin_zonage';
            if (isset($interests_and_needs['besoin_zonage'])&&$besoin_zonage_attribute){
                $output->writeln('import attribute besoin_zonage',OutputInterface::VERBOSITY_VERBOSE);
                $att_value = strtolower(trim($interests_and_needs['besoin_zonage']));
                if (strlen($att_value) >= 1 ){
                    $values = explode('-',$att_value);
                    if ($values[0]){
                        if (!isset($values[1]))
                            $values[1] = $values[0];
                        for ($i=$values[0];$i<=$values[1];$i++){
                            /** @var AttributeValue $value */
                            $value = $this->entityManager->getRepository(AttributeValue::class)->findOneBy(array('attribute'=>$besoin_zonage_attribute,'value'=>$i));
                            if ($value){
                                $new_plant->addAttributeValue($value);
                            }
                        }
                    }
                }
            }
            //'graine_comestible','graine_grignotte','graine_cuite','graine_farine','graine_sechee','graine_huile','graine_germee',
            //'fruit_comestible','fruit_de_table','fruit_grignotte','fruit_transforme','fruit_conserve_par_cuisson','fruit_boisson',
            //'fruit_congelation','fruit_superfood','fruit_de_garde'
            foreach ($multivalue_custom_attributes as $c => $data){
                $output->writeln('import attribute '.$c,OutputInterface::VERBOSITY_VERBOSE);
                foreach ($data['values'] as $code => $value){
                    if (isset($interests_and_needs[$code])){
                        $att_value = strtolower(trim($interests_and_needs[$code]));
                        if (strlen($att_value) >= 1 ){
                            $new_plant->addAttributeValue($value);
                            $new_plant->addAttributeValue($comestible_value);
                        }
                    }
                }
            }
            //        'multiplication';
            //        'multiplication_info';
            //        'toxicite';
            //        'toxicite_info';
            foreach ($info_attributes as $code => $attributes){
                $output->writeln('import attribute '.$code,OutputInterface::VERBOSITY_VERBOSE);
                if (isset($interests_and_needs[$code])){
                    $att_value = strtolower(trim($interests_and_needs[$code]));
                    if (strlen($att_value) >= 1 ){
                        /** @var AttributeValue $av */
                        $av = $this->entityManager
                                ->getRepository(AttributeValue::class)
                                ->findOneBy(array('attribute'=>$attributes['attr'],'value'=>null));
                        if (!$av){
                            $av = $this->newValue('',$attributes['attr']);
                        }
                        $new_plant->addAttributeValue($av); //no value

                        //info
                        $info = $this->newValue($att_value,$attributes['info']);
                        $new_plant->addAttributeValue($info);
                    }
                }
            }
            $multiplication_attr = $this->newAttribute('multiplication');
            foreach (array('bouturage','semi','division','greffe') as $v){
                $this->newValue($v,$multiplication_attr);
            }

            $petiole_feuille_comestible_attr = $this->newAttribute('petiole_feuille_comestible');
            foreach (array('petiole fraiche','feuille fraiche','petiole sèche','feuille sèche','petiole cuite','feuille cuite') as $v){
                $this->newValue($v,$petiole_feuille_comestible_attr);
            }

            $toxicite_attr = $this->newAttribute('toxicite');
            foreach (array('toxique','non toxique','toxique à forte dose') as $v){
                $this->newValue($v,$toxicite_attr);
            }

            $this->entityManager->persist($new_plant);
            $this->entityManager->flush();

            $progressBar->advance();
            $counter++;
            if ($counter>=$limit){
                break;
            }
        }
        $progressBar->finish();
        $this->entityManager->flush();

        $this->entityManager->flush();

        $properties = array(
            'rusticity'=>array('position'=>0,'name'=>'résistance au froid (rusticité)'),
            'rusticity_comment'=>array('position'=>1,'name'=>'commentaire'),
            'native_place'=>array('position'=>1,'name'=>'origine'),
            'height_width'=>array('position'=>1,'name'=>'hauteur - largeur'),
            'gallery'=>array('position'=>3,'name'=>''),
            'flowerings_crops'=>array('position'=>2,'name'=>'floraison et fructification'),
            'sources'=>array('position'=>1,'name'=>'Sources'),
        );
        foreach ($properties as $code => $data){
            $prop = new Property();
            $prop->setCode($code);
            $prop->setPosition($data['position']);
            $prop->setName($data['name']);
            $this->entityManager->persist($prop);
        }
        $this->entityManager->flush();

        //families and category
        $families =
            array(
                'Biotope (environnement) // secteurs du terrain'=>array(
                    'Conditions pédoclimatiques' => array('attributes'=>array('drought_tolerance','wind_stopper','insolation','humus','soil','ph','limestone')
                    ,'property'=>array('rusticity')),
                    'Milieu naturel et sub naturel' => array('property'=>array('origin')),
                ),
                'Besoins éco-systèmiques'=>array(
                    'Auxiliaires' => array('attributes'=>array('besoin_pollinisation')),
                    'Plantes Compagnes' => array('attributes'=>array()),
                ),
                'Besoins culturaux'=>array(
                    'Soin'=> array('attributes'=>array('besoin_arrosage','besoin_taille','besoin_limitation_de_concurrence','besoin_azote',
                        'besoin_conduite','besoin_cultural_entretien','besoin_cultural_exigences_particulieres','besoin_matiere_organique',
                        'besoin_mineraux','besoin_protection','besoin_brise_vent','besoin_tuteur','maladies_ravageurs','besoin_zonage')),
                    'Récolte'=> array('attributes'=>array('besoin_cultural_cueillette_ramassage','besoin_cultural_precisions_recolte',
                        'besoin_cultural_recolte_etalee_groupee')),
                    'Multiplication'=> array('attributes'=>array('multiplication','multiplication_info')),
                ),
                'Description'=>array(
                    '_' => array('attributes'=>array('stratum')),
                    'Mensuration' => array('property'=>array('height_width')),
                    'Forme' => array('attributes'=>array('port','foliage','root'),'property'=>array('gallery')),
                    'Sources' => array('property'=>array('sources')),
                ),
                'Cycle'=>array(
                    '_' => array('attributes'=>array('life_cycle',),'property'=>array('flowerings_crops')),
                ),
                'Services éco-systèmiques'=>array(
                    'Fértilité du sol'=>array('attributes'=>array('interet_fixateur_dazote','interet_fixateur_de_mineraux','precisions_fixateur_de_mineraux',
                        'interet_allelopathique','interet_amelioration_du_compost','interet_amelioration_structure_du_sol',
                        'interet_biomasse','interet_anti_erosion')),
                    'Faune'=>array('attributes'=>array('interet_habitat_d_oiseaux','interet_insectes_auxilliaires','precisions_insectes_auxilliaires',
                        'interet_mellifere','interet_nectarifere','interet_pollen','interet_repulsif_insectes')),
                    'Micro-climats'=>array('attributes'=>array('interet_ombre_legere','interet_couvre_sol','interet_haie','interet_pionnier'))
                ),
                'Services culturaux'=>array(
                    'Allimentation / Santé'=>array('attributes'=>array('toxicite','toxicite_info','fruit_comestible','graine_comestible','fleur_comestible',
                        'petiole_feuille_comestible','bourgeon_seve_comestible','rhizome_tubercule_bulbe_comestible','usage_medicinal',
                        'proprietes_medicinales','associations_culinaires')),
                    'Animaux'=>array('attributes'=>array('fourrage_basse_court','fourrage_grands_herbivores','precision_fourrage')),
                    'Plaisir des sens'=>array('attributes'=>array('odorante','ornementale')),
                    'Usages artisanaux et domestique'=>array('attributes'=>array('vannerie','bois_oeuvre','tuteur','tinctoriale','bois_chauffage',
                        'usage_aromatique','porte_greffe')),
                )
            );
        foreach ($families as $family_name => $children){
            $family = $this->newAttributeFamily($family_name);
            foreach ($children as $name => $data){
                if (isset($data['attributes'])){
                    $attributes = $data['attributes'];
                    $family_child = $this->newAttributeFamily($name,$family);
                    foreach ($attributes as $attribute_code){
                        /** @var Attribute $attribute */
                        $attribute = $this->entityManager->getRepository(Attribute::class)->findOneBy(array('code'=>$attribute_code));
                        if ($attribute){
                            $attribute->setFamily($family_child);
                            $this->entityManager->persist($attribute);
                        }
                    }
                }
                if (isset($data['property'])){
                    $properties = $data['property'];
                    $family_child = $this->newAttributeFamily($name,$family);
                    foreach ($properties as $code){
                        /** @var Property $property */
                        $property = $this->entityManager->getRepository(Property::class)->findOneBy(array('code'=>$code));
                        if ($property){
                            $property->setFamily($family_child);
                            $this->entityManager->persist($property);
                        }
                    }
                }
            }
        }

        $filter_categories = array(
            'mon sol' => array('attributes'=>array('humus','soil','ph')),
            'mes conditions climatiques' => array('attributes'=>array('insolation','drought_tolerance'),'properties'=>array('rusticity')),
            'créer un micro climat' => array('attributes'=>array('wind_stopper','leaf_density','foliage')),
            'selon calendrier' => array('attributes'=>array('flowering','interet_mellifere','interet_nectarifere','interet_pollen','fruit_comestible','crop','stratum')),
            'pour une haie' => array('attributes'=>array('interet_haie','comestible')),
//            'Recherches avancées' => array(),
        );
        //
        foreach ($filter_categories as $name => $data){
            $category = new FilterCategory();
            $category->setName($name);
            if (isset($data['attributes'])){
                foreach ($data['attributes'] as $code){
                    /** @var Attribute $attribute */
                    $attribute = $this->entityManager->getRepository(Attribute::class)->findOneBy(array('code'=>$code));
                    if ($attribute){
                        $category->addPropertyOrAttribute($attribute);
                    }
                }
            }
            if (isset($data['properties'])) {
                foreach ($data['properties'] as $code) {
                    /** @var Property $property */
                    $property = $this->entityManager->getRepository(Property::class)->findOneBy(array('code' => $code));
                    if ($property) {
                        $category->addPropertyOrAttribute($property);
                    }
                }
            }
            $this->entityManager->persist($category);
        }
        $this->entityManager->flush();

        $output->writeln('');

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

    private function newMainValue(Attribute $attribute, string $label){
        $exist = null;
        if (isset($this->attribute_main_value[$attribute->getCode()])){
            $exist = $this->attribute_main_value[$attribute->getCode()];
        }
        if (!$exist) {
            $exist = $this->entityManager->getRepository(MainValue::class)->findOneBy(array('attribute' => $attribute));
            if (!$exist) {
                $main_value = new MainValue();
                $main_value->setLabel(strtolower($label));
                $main_value->setAttribute($attribute);
                $this->entityManager->persist($main_value);
                $this->attribute_main_value[$attribute->getCode()] = $main_value;
                return $main_value;
            }
        }
        return $exist;
    }

    /**
     * @param string $code
     * @return Attribute|null
     */
    private function newAttribute(string $code){
        $exist = null;
        if (isset($this->attribute[$code])){
            $exist = $this->attribute[$code];
        }
        if (!$exist) {
            $exist = $this->entityManager->getRepository(Attribute::class)->findOneBy(array('code' => $code));
            if (!$exist) {
                $a = new Attribute();
                if (!isset($this->attribute_map[$code])) {
                    return null;
                }
                $attr = $this->attribute_map[$code];
                $a->setCode($code);
                $a->setType($attr['type']);
                $a->setName(strtolower($attr['name']));
                $this->entityManager->persist($a);
                $this->attribute[$code] = $a;
                return $a;
            }
        }
        return $exist;
    }

    /**
     * @param string $label
     * @param Attribute $attribute
     * @param string $code
     * @return AttributeValue
     */
    private function newValue(string $label, Attribute $attribute, string $code = ''){
        $name = substr($label,0,255);
        $name = strtolower($name);
        if (!$code){
            $code = $name;
        }
        $code = Plant::makeSlug($code);
        if (!$code || strlen($code)===0){
            $code = 'none';
        }
        /** @var AttributeValue $exist */
        $exist = null;
        if (isset($this->attribute_values[$attribute->getCode().'_'.$code])){
            $exist = $this->attribute_values[$attribute->getCode().'_'.$code];
        }
        if (!$exist){
            if (strlen($name)===0)
                $name = null;
            $exist = $this->entityManager->getRepository(AttributeValue::class)->findOneBy(array('attribute'=>$attribute,'code'=>$code));
            if (!$exist) {
                $value = new AttributeValue();
                if ($name)
                    $value->setValue($name);
                $value->setCode($code);
                $value->setAttribute($attribute);
                $this->entityManager->persist($value);
                $this->attribute_values[$attribute->getCode().'_'.$code] = $value;
                return $value;
            }
        }
        return $exist;
    }

    /**
     * @param string $name
     * @param AttributeFamily|null $parent
     * @return AttributeFamily|null
     */
    private function newAttributeFamily(string $name,AttributeFamily $parent = null){
        $name = strtolower($name);
        $exist = null;
        $parent_code = ($parent) ? Plant::makeSlug($parent->getName()) : 'orphan';
        if (isset($this->attribute_family[$parent_code.'_'.$name])){
            $exist = $this->attribute_family[$parent_code.'_'.$name];
            if ($exist->getParent() != $parent){
                // what to do ?
            }
        }
        if (!$exist) {
            $exist = $this->entityManager->getRepository(AttributeFamily::class)->findOneBy(array('name' => $name));
            if (!$exist) {
                $af = new AttributeFamily();
                $af->setName($name);
                if ($parent)
                    $af->setParent($parent);
                $this->entityManager->persist($af);
                $this->attribute_family[$parent_code.'_'.$name] = $af;
                return $af;
            }
            if ($exist->getParent() != $parent){
                // what to do ?
            }
        }
        return $exist;
    }


}