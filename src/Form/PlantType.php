<?php

namespace App\Form;

use App\Entity\Clay;
use App\Entity\Humidity;
use App\Entity\Humus;
use App\Entity\Nutrient;
use App\Entity\Ph;
use App\Entity\Plant;
use App\Entity\PlantFamily;
use App\Entity\Soil;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('latin_name',TextType::class,['label'=>'nom latin'])
            ->add('name')
            ->add('life_cycle',ChoiceType::class,[
                'choices'  => [
                    '' => Plant::LIFE_CYCLE_UNDEFINED,
                    'plante annuelle' => Plant::LIFE_CYCLE_ANNUAL,
                    'plante biannuelle' => Plant::LIFE_CYCLE_BIENNIAL,
                    'plante vivace' => Plant::LIFE_CYCLE_PERENNIAL,
                ], 'label' => 'cycle de vie'
            ])
            ->add('images', FileType::class,[
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])
            ->add('stratum',ChoiceType::class,[
                'choices'  => [
                    '' => Plant::STRATUM_UNDEFINED,
                    'basse' => Plant::STRATUM_LOW,
                    'arbrisseau' => Plant::STRATUM_SHRUB,
                    'médiane' => Plant::STRATUM_MEDIUM,
                    'arbre' => Plant::STRATUM_TREE,
                    'canopée' => Plant::STRATUM_CANOPY,
                    'grimpante' => Plant::STRATUM_CLIMBING,
                ], 'label' => 'strate'
            ])
            ->add('rusticity')
            ->add('rusticity_comment')
            ->add('temperature')
            ->add('root',ChoiceType::class,[
                'choices'  => [
                    '' => Plant::ROOT_UNDEFINED,
                    'traçantes' => Plant::ROOT_CREEPING,
                    'fasciculées' => Plant::ROOT_FASCICULE,
                    'mixtes' => Plant::ROOT_MIXED,
                    'bulbes' => Plant::ROOT_BULB,
                    'pivot' => Plant::ROOT_TAPROOT,
                    'tubercules' => Plant::ROOT_TUBER,
                ], 'label' => 'racines'
            ])
            ->add('min_height')
            ->add('max_height')
//            ->add('woody',CheckboxType::class,["required"=>false])
            ->add('min_width')
            ->add('max_width')
            ->add('sucker')
            ->add('limestone')
            ->add('min_sexual_maturity')
            ->add('max_sexual_maturity')
            ->add('native_place',TextType::class,['label'=>'origine',"required"=>false])
//            ->add('botany_leaf')
//            ->add('botany_branch')
//            ->add('botany_root')
//            ->add('botany_flower')
//            ->add('botany_fruit')
//            ->add('botany_seed')
            ->add('density')
            ->add('leaf_density')
            ->add('foliage')
            ->add('interest')
            ->add('specificity')
            ->add('priority')
            ->add('drought_tolerance')
            ->add('diseases_and_pest')
            ->add('author')
            ->add('family', EntityType::class, array(
                'class' => PlantFamily::class,
                'choice_label' => 'name',
                'required' => false
            ))
            ->add('soils', EntityType::class, array(
                'class' => Soil::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false
            ))
            ->add('phs', EntityType::class, array(
                'class' => Ph::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false
            ))
            ->add('nutrients', EntityType::class, array(
                'class' => Nutrient::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false
            ))
            ->add('clays', EntityType::class, array(
                'class' => Clay::class,
                'choice_label' => 'scale',
                'multiple' => true,
                'required' => false
            ))
            ->add('humuses', EntityType::class, array(
                'class' => Humus::class,
                'choice_label' => 'quantity',
                'multiple' => true,
                'required' => false
            ))
            ->add('humidities', EntityType::class, array(
                'class' => Humidity::class,
                'choice_label' => 'value',
                'multiple' => true,
                'required' => false
            ))
            //->add('attributes')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Plant::class,
        ]);
    }
}
