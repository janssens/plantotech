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
use App\Repository\AttributeFamilyRepository;
use App\Repository\PlantFamilyRepository;
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
    private $_familyRepository;

    public function __construct(PlantFamilyRepository $familyRepository)
    {
        $this->_familyRepository = $familyRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('latin_name',TextType::class,['label'=>'nom latin'])
            ->add('name')
            ->add('rusticity')
            ->add('rusticity_comment')
            ->add('temperature')
            ->add('woody',CheckboxType::class,["required"=>false,"false_values"=>[null,0]])
            ->add('min_width')
            ->add('max_width')
            ->add('min_height')
            ->add('max_height')
            ->add('min_sexual_maturity')
            ->add('max_sexual_maturity')
            ->add('native_place',TextType::class,['label'=>'origine',"required"=>false])
            ->add('botany_leaf')
            ->add('botany_branch')
            ->add('botany_root')
            ->add('botany_flower')
            ->add('botany_fruit')
            ->add('botany_seed')
            ->add('density')
            ->add('interest')
            ->add('specificity')
            ->add('author')
            ->add('family', EntityType::class, array(
                'class' => PlantFamily::class,
                'choices' => $this->_familyRepository->findAlphabetical(),
                'placeholder' => '-- Choisissez une famille --',
                'choice_label' => 'name',
                'required' => false
            ))
            ->add('images', FileType::class,[
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])
            //->add('sources')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Plant::class,
        ]);
    }
}
