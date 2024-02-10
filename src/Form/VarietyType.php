<?php

namespace App\Form;

use App\Entity\Plant;
use App\Entity\Variety;
use App\Repository\PlantRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VarietyType extends AbstractType
{
    private $_plantRepository;

    public function __construct(PlantRepository $plantRepository)
    {
        $this->_plantRepository = $plantRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parent', EntityType::class, array(
                'class' => Plant::class,
                'choices' => $this->_plantRepository->findAlphabetical(),
                'placeholder' => '-- Choisissez un plante parent --',
                'choice_label' => 'name',
                'required' => true
            ))
            ->add('name',TextType::class,['label'=>'nom vernaculaire','required' => true ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Variety::class,
        ]);
    }
}
