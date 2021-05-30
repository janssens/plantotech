<?php

namespace App\Form;

use App\Entity\AttributeFamily;
use App\Entity\FilterCategory;
use App\Entity\PropertyOrAttribute;
use App\Repository\AttributeFamilyRepository;
use App\Repository\FilterCategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertyOrAttributeType extends AbstractType
{
    private $_attributeFamilyRepository;
    private $_filterCategoryRepository;

    public function __construct(AttributeFamilyRepository $attributeFamilyRepository,FilterCategoryRepository $filterCategoryRepository)
    {
        $this->_attributeFamilyRepository = $attributeFamilyRepository;
        $this->_filterCategoryRepository = $filterCategoryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code')
            ->add('name')
            ->add('family', EntityType::class, array(
                'class' => AttributeFamily::class,
                'choices' => $this->_attributeFamilyRepository->findAlphabetical(),
                'placeholder' => '-- Choisissez une famille --',
                'choice_label' => 'name',
                'required' => false
            ))->add('filterCategory', EntityType::class, array(
                'class' => FilterCategory::class,
                'choices' => $this->_filterCategoryRepository->findAlphabetical(),
                'placeholder' => '-- Choisissez un filtre --',
                'choice_label' => 'name',
                'required' => false
            ));
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PropertyOrAttribute::class,
        ]);
    }
}
