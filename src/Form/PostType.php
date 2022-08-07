<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Category;
use App\Entity\Post;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('locale', ChoiceType::class, [
                'choices'  => [
                    'en' => 'en',
                    'fr' => 'fr',
                    'ar' => 'ar',
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('mainPost')
            ->add('title')
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'asset_helper' => true,
            ])
            ->add('excerpt')
            ->add('content')
            ->add('author', EntityType::class, [
                'class' => Author::class,
                'choice_label' => 'name',
                'placeholder' => '',
                'required' => false,
                'autocomplete' => true
            ])
            ->add('keywords', KeywordAutocompleteField::class, [
                'required' => true,
                'multiple' => true,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'query_builder' => function (CategoryRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->andWhere('c.locale = :locale')
                        ->setParameter('locale', 'en');
                },
                'choice_label' => 'name',
                'placeholder' => '',
                'required' => false,
                'autocomplete' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
