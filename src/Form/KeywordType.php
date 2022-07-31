<?php

namespace App\Form;

use App\Entity\Keyword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KeywordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('posts')
            ->add('locale', ChoiceType::class, [
                'choices'  => [
                    'en' => 'en',
                    'fr' => 'fr',
                    'ar' => 'ar',
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('mainKeyword', KeywordAutocompleteField::class, [
                'required' => true,
                'multiple' => false,

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Keyword::class,
        ]);
    }
}
