<?php

namespace App\Form;

use App\Entity\Keyword;
use App\Repository\KeywordRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField]
class KeywordAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Keyword::class,
            'placeholder' => 'Choose a Keyword',
            'choice_label' => 'name',
            'query_builder' => function(KeywordRepository $keywordRepository) {
                return $keywordRepository->createQueryBuilder('keyword')
                    ->andWhere('keyword.locale = :locale')
                    ->setParameter('locale', 'en');

            },
            //'security' => 'ROLE_SOMETHING',
        ]);
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}
