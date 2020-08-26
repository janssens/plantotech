<?php

namespace App\Form;

use App\Entity\Plant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('latin_name')
            ->add('name')
            ->add('life_cycle')
            ->add('rusticity')
            ->add('rusticity_comment')
            ->add('temperature')
            ->add('woody')
            ->add('min_height')
            ->add('max_height')
            ->add('root')
            ->add('min_width')
            ->add('max_width')
            ->add('sucker')
            ->add('limestone')
            ->add('min_sexual_maturity')
            ->add('max_sexual_maturity')
            ->add('native_place')
            ->add('botany_leaf')
            ->add('botany_branch')
            ->add('botany_root')
            ->add('botany_flower')
            ->add('botany_fruit')
            ->add('botany_seed')
            ->add('density')
            ->add('leaf_density')
            ->add('foliage')
            ->add('interest')
            ->add('specificity')
            ->add('priority')
            ->add('drought_tolerance')
            ->add('diseases_and_pest')
            ->add('author')
            ->add('stratum')
            ->add('family')
            ->add('soils')
            ->add('phs')
            ->add('nutrients')
            ->add('clays')
            ->add('humuses')
            ->add('humidities')
            ->add('attributes')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Plant::class,
        ]);
    }
}
