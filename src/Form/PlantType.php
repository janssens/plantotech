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
            ->add('family', EntityType::class, array(
                'class' => PlantFamily::class,
                'choices' => $this->_familyRepository->findAlphabetical(),
                'placeholder' => '-- Choisissez une famille --',
                'choice_label' => 'name',
                'required' => false
            ));
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Plant::class,
        ]);
    }
}
