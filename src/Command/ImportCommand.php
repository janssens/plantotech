<?php
// src/Command/ImportCommand.php
namespace App\Command;

use App\Entity\Attribute;
use App\Entity\AttributeFamily;
use App\Entity\AttributeValues;
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
    private $attribute;
    private $attribute_map;
    private $attribute_values;
    private $attribute_family;
    private $excluded_from_auto_import;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->attribute = array();
        $this->attribute_values = array();
        $this->attribute_family = array();
        $this->attribute_map = $attribute_map = array(
            'besoin_brise_vent'=> array('name' => 'a protéger du vent','type' => Attribute::TYPE_NONE),
            'besoin_pollinisation'=> array('name' => 'pollinisation','type' => Attribute::TYPE_UNIQUE),
            'besoin_limitation_de_concurrence'=> array('name' => 'limiter la concurrence','type' => Attribute::TYPE_MULTIPLE),
            'besoin_azote'=> array('name' => 'Besoin en azote','type' => Attribute::TYPE_NONE),
            'besoin_mineraux'=> array('name' => 'Besoin en minéraux','type' => Attribute::TYPE_SINGLE),
            'besoin_matiere_organique'=> array('name' => 'Besoin en matière organique','type' => Attribute::TYPE_SINGLE),
            'besoin_zonage'=> array('name' => 'zonage','type' => Attribute::TYPE_MULTIPLE),
            'besoin_protection'=> array('name' => 'protection','type' => Attribute::TYPE_MULTIPLE),
            'besoin_taille'=> array('name' => 'taille','type' => Attribute::TYPE_MULTIPLE),
            'besoin_arrosage'=> array('name' => 'arrosage','type' => Attribute::TYPE_SINGLE),
            'besoin_tuteur'=> array('name' => 'Besoin tuteur','type' => Attribute::TYPE_NONE),
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
            'interet_amelioration_structure_du_sol' => array('name' =>  'amélioration de la structure du sol','type' => Attribute::TYPE_NONE),
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
            'tuteur' => array('name' =>  'Peut servir de tuteur','type' => Attribute::TYPE_NONE),
            'vannerie' => array('name' =>  'vannerie','type' => Attribute::TYPE_MULTIPLE),
            'bois_chauffage' => array('name' =>  'bois de chauffage','type' => Attribute::TYPE_NONE),
            'porte_greffe' => array('name' =>  'Peut servir de porte greffe','type' => Attribute::TYPE_NONE),
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
            'brise_vent' => array('name' =>  'Brise vent','type' => Attribute::TYPE_NONE),
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
            'besoin_zonage',
            'maladies_ravageurs',
            'besoin_protection',
            'usage_medicinal',
            'besoin_taille',
            'besoin_conduite',
            'usage_aromatique',
            'comestible'
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

        $multivalue_custom_attributes = array();
        //'graine_comestible','graine_grignotte','graine_cuite','graine_farine','graine_sechee','graine_huile','graine_germee',
        $multivalue_custom_attributes['graine_comestible'] = array();
        $graine_comestible_attribute = $this->newAttribute('graine_comestible');
        $multivalue_custom_attributes['graine_comestible']['values'] = array();
        foreach (array('graine_grignotte','graine_cuite','graine_farine','graine_sechee','graine_huile','graine_germee') as $code){
            $multivalue_custom_attributes['graine_comestible']['values'][$code] = $this->newValue($code,$graine_comestible_attribute);
        }
        $multivalue_custom_attributes['graine_comestible']['attr'] = $graine_comestible_attribute;
        //'fruit_comestible','fruit_de_table','fruit_grignotte','fruit_transforme','fruit_conserve_par_cuisson','fruit_boisson','fruit_congelation','fruit_superfood','fruit_de_garde'
        $multivalue_custom_attributes['fruit_comestible'] = array();
        $fruit_comestible_attribute = $this->newAttribute('fruit_comestible');
        $multivalue_custom_attributes['fruit_comestible']['values'] = array();
        foreach (array('fruit_de_table','fruit_grignotte','fruit_transforme','fruit_conserve_par_cuisson','fruit_boisson','fruit_congelation','fruit_superfood','fruit_de_garde') as $code){
            $multivalue_custom_attributes['fruit_comestible']['values'][$code] = $this->newValue($code,$fruit_comestible_attribute);
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
        for ($i=0;$i<6;$i++){
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

        $this->entityManager->flush();

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
            foreach ($flowering_crop_type_map as $key_type => $type_value){
                $db = clone $clean_db;
                $db->where("nom_latin", $plant[SELF::PLANT_KEY_LATIN_NAME]);
                $db->where("floraison_ou_recolte", $key_type);
                $values = $db->getOne("PLANTE_FLORAISONS_RECOLTES","*");
                foreach ($flowering_crop_month_map as $month => $index){
                    if (isset($values[$month])) {
                        if (strlen($values[$month] ) > 0) {

                            $flowering_crop = new FloweringAndCrop();
                            $flowering_crop->setType($type_value);
                            $flowering_crop->setMonth($index);
                            //$flowering_crop->setPlant($new_plant);
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
            foreach ($this->attribute_map as $key => $value){
                if (!in_array($key,$this->excluded_from_auto_import)){
                    $output->writeln('import attribute '.$key);
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
                                    $new_plant->addAttribute($attribute_value);
                                    break;
                                case Attribute::TYPE_UNIQUE :
                                case Attribute::TYPE_MULTIPLE :
                                    $attribute_value = $this->newValue($att_value,$attribute);
                                    $new_plant->addAttribute($attribute_value);
                                    break;
                                case Attribute::TYPE_SINGLE :
                                    /** @var AttributeValues $attribute_value */
                                    $attribute_value = $this->newValue($att_value,$attribute);
                                    $new_plant->addAttribute($attribute_value);
                                    break;
                                default:
                            }
                        }
                    }
                }
            }
            foreach (array('usage_aromatique','besoin_conduite','besoin_taille','usage_medicinal','besoin_protection','maladies_ravageurs','interet_haie','bourgeon_seve_comestible') as $code ){
                $output->writeln('import attribute '.$code);
                /** @var Attribute $multivalue_custom_attr */
                $multivalue_custom_attr = $this->entityManager->getRepository(Attribute::class)->findOneBy(array('code'=>$code));
                if (isset($interests_and_needs[$code])&&$multivalue_custom_attr){
                    $att_value = strtolower(trim($interests_and_needs[$code]));
                    if (strlen($att_value) >= 1 ){
                        foreach ($multivalue_custom_attr->getAvailableValues() as $value){
                            if (strpos($att_value,$value->getValue())>0 || strpos($att_value,$value->getValue()) === 0){
                                $new_plant->addAttribute($value);
                            }
                        }
                    }
                }
            }
            //'besoin_zonage';
            if (isset($interests_and_needs['besoin_zonage'])&&$besoin_zonage_attribute){
                $output->writeln('import attribute besoin_zonage');
                $att_value = strtolower(trim($interests_and_needs['besoin_zonage']));
                if (strlen($att_value) >= 1 ){
                    $values = explode('-',$att_value);
                    if (!isset($values[1]))
                        $values[1] = $values[0];
                    for ($i=$values[0];$i<=$values[1];$i++){
                        /** @var AttributeValues $value */
                        $value = $this->entityManager->getRepository(AttributeValues::class)->findOneBy(array('attribute'=>$besoin_zonage_attribute,'value'=>$i));
                        if ($value){
                            $new_plant->addAttribute($value);
                        }
                    }
                }
            }
            //'graine_comestible','graine_grignotte','graine_cuite','graine_farine','graine_sechee','graine_huile','graine_germee',
            //'fruit_comestible','fruit_de_table','fruit_grignotte','fruit_transforme','fruit_conserve_par_cuisson','fruit_boisson','fruit_congelation','fruit_superfood','fruit_de_garde'
            foreach ($multivalue_custom_attributes as $c => $data){
                $output->writeln('import attribute '.$c);
                foreach ($data['values'] as $code => $value){
                    if (isset($interests_and_needs[$code])){
                        $att_value = strtolower(trim($interests_and_needs[$code]));
                        if (strlen($att_value) >= 1 ){
                            $new_plant->addAttribute($value);
                        }
                    }
                }
            }
            //        'multiplication';
            //        'multiplication_info';
            //        'toxicite';
            //        'toxicite_info';
            foreach ($info_attributes as $code => $attributes){
                $output->writeln('import attribute '.$code);
                if (isset($interests_and_needs[$code])){
                    $att_value = strtolower(trim($interests_and_needs[$code]));
                    if (strlen($att_value) >= 1 ){
                        /** @var AttributeValues $av */
                        $av = $this->entityManager
                                ->getRepository(AttributeValues::class)
                                ->findOneBy(array('attribute'=>$attributes['attr'],'value'=>null));
                        if (!$av){
                            $av = new AttributeValues();
                            $av->setAttribute($attributes['attr']);
                            $av->setValue(null);
                            $this->entityManager->persist($av);
                        }
                        $new_plant->addAttribute($av); //no value

                        //info
                        $info = new AttributeValues();
                        $info->setAttribute($attributes['info']);
                        $info->setValue(substr($att_value,0,255));
                        $this->entityManager->persist($info);
                        $new_plant->addAttribute($info);
                    }
                }
            }
            $multiplication_attr = $this->newAttribute('multiplication');
            foreach (array('bouturage','semi','division','greffe') as $v){
                $av = new AttributeValues();
                $av->setAttribute($multiplication_attr);
                $av->setValue($v);
                $this->entityManager->persist($av);
            }

            $petiole_feuille_comestible_attr = $this->newAttribute('petiole_feuille_comestible');
            foreach (array('petiole fraiche','feuille fraiche','petiole sèche','feuille sèche','petiole cuite','feuille cuite') as $v){
                $av = new AttributeValues();
                $av->setAttribute($petiole_feuille_comestible_attr);
                $av->setValue($v);
                $this->entityManager->persist($av);
            }

            $toxicite_attr = $this->newAttribute('toxicite');
            foreach (array('toxique','non toxique','toxique à forte dose') as $v){
                $av = new AttributeValues();
                $av->setAttribute($toxicite_attr);
                $av->setValue($v);
                $this->entityManager->persist($av);
            }

            $this->entityManager->persist($new_plant);
            $this->entityManager->flush();
        }
        $families =
            array(
                'Besoins éco-systèmiques'=>array(
                    'Auxiliaires' => array('besoin_pollinisation'),
                    'Plantes Compagnes' => array(),
                ),
                'Besoins culturaux'=>array(
                    'Soin'=> array('besoin_arrosage','besoin_taille','besoin_limitation_de_concurrence','besoin_azote',
                        'besoin_conduite','besoin_cultural_entretien','besoin_cultural_exigences_particulieres','besoin_matiere_organique',
                        'besoin_mineraux','besoin_protection','besoin_brise_vent','besoin_tuteur','maladies_ravageurs','besoin_zonage'),
                    'Récolte'=> array('besoin_cultural_cueillette_ramassage','besoin_cultural_precisions_recolte','besoin_cultural_recolte_etalee_groupee'),
                    'Multiplication'=> array('multiplication','multiplication_info'),
                ),
                'Services éco-systèmiques'=>array(
                    'Fértilité du sol'=>array('interet_fixateur_dazote','interet_fixateur_de_mineraux','precisions_fixateur_de_mineraux',
                        'interet_allelopathique','interet_amelioration_du_compost','interet_amelioration_structure_du_sol',
                        'interet_biomasse','interet_anti_erosion'),
                    'Faune'=>array('interet_habitat_d_oiseaux','interet_insectes_auxilliaires','precisions_insectes_auxilliaires',
                        'interet_mellifere','interet_nectarifere','interet_pollen','interet_repulsif_insectes'),
                    'Micro-climats'=>array('interet_ombre_legere','interet_couvre_sol','interet_haie','interet_pionnier')
                ),
                'Services culturaux'=>array(
                    'Allimentation / Santé'=>array('toxicite','toxicite_info','fruit_comestible','graine_comestible','fleur_comestible',
                        'petiole_feuille_comestible','bourgeon_seve_comestible','rhizome_tubercule_bulbe_comestible','usage_medicinal',
                        'proprietes_medicinales','associations_culinaires'),
                    'Animaux'=>array('fourrage_basse_court','fourrage_grands_herbivores','precision_fourrage'),
                    'Plaisir des sens'=>array('odorante','ornementale'),
                    'Usages artisanaux et domestique'=>array('vannerie','bois_oeuvre','tuteur','tinctoriale','bois_chauffage','usage_aromatique','porte_greffe'),
                )
            );
        foreach ($families as $family_name => $children){
            $family = $this->newAttributeFamily($family_name);
            foreach ($children as $name => $attributes){
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
        }
        $this->entityManager->flush();

        $comestible = $this->newAttribute('comestible');
        $comestible_value = $this->newValue('',$comestible);
        foreach (array('fruit_comestible','graine_comestible','petiole_feuille_comestible','rhizome_tubercule_bulbe_comestible','fleur_comestible','bourgeon_seve_comestible') as $code){
            $attribute = $this->newAttribute($code);
            foreach ($attribute->getAvailableValues() as $value){
                foreach ($value->getPlants() as $plant){
                    $comestible_value->addPlant($plant);
                    $this->entityManager->persist($plant);
                }
            }
        }
        $this->entityManager->persist($comestible_value);
        $this->entityManager->flush();

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
                $a->setName($attr['name']);
                $this->entityManager->persist($a);
                $this->attribute[$code] = $a;
                return $a;
            }
        }
        return $exist;
    }

    /**
     * @param string $code
     * @param Attribute $attribute
     * @return AttributeValues
     */
    private function newValue(string $code, Attribute $attribute){
        $value = new AttributeValues();
        $name = substr($code,0,255);
        if (isset($this->attribute_map[$code])){
            $name = $this->attribute_map[$code]['name'];
        }
        /** @var AttributeValues $exist */
        $exist = null;
        if (isset($this->attribute_values[$attribute->getCode().'_'.$name])){
            $exist = $this->attribute_values[$attribute->getCode().'_'.$name];
        }
        if (!$exist){
            if (!$name)
                $name = null;
            $exist = $this->entityManager->getRepository(AttributeValues::class)->findOneBy(array('attribute'=>$attribute,'value'=>$name));
            if (!$exist) {
                if ($name)
                    $value->setValue($name);
                $value->setAttribute($attribute);
                $this->entityManager->persist($value);
                $this->attribute_values[$attribute->getCode().'_'.$name] = $value;
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
        $exist = null;
        if (isset($this->attribute_family[$name])){
            $exist = $this->attribute_family[$name];
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
                $this->attribute_family[$name] = $af;
                return $af;
            }
            if ($exist->getParent() != $parent){
                // what to do ?
            }
        }
        return $exist;
    }
}