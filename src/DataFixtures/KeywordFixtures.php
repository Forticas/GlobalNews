<?php

namespace App\DataFixtures;

use App\Entity\Keyword;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class KeywordFixtures extends Fixture
{

    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker_en = Factory::create('en_US');
        $faker_fr = Factory::create('fr_FR');
        $faker_ar = Factory::create('ar_SA');
        // generate 500 keywords and fr and ar other keywords where main category is en without slug
        for ($i = 0; $i < 500; $i++) {
            $keyword_en = new Keyword();
            $keyword_en->setName($faker_en->words(random_int(1, 3), true).'-'.$i.'en');
            $manager->persist($keyword_en);
            try {
                $manager->flush();

            }catch (UniqueConstraintViolationException $e) {
                $manager = $this->em::create(
                    $manager->getConnection(),
                    $manager->getConfiguration()
                );
                continue;
            }

            $keyword_fr = new Keyword();
            $keyword_fr->setName($faker_fr->words(random_int(1, 3), true).'-'.$i.'fr');
            $keyword_fr->setLocale('fr');
            $keyword_fr->setMainKeyword($keyword_en);
            $manager->persist($keyword_fr);
            try {
                $manager->flush();

            }catch (UniqueConstraintViolationException $e) {
                $manager = $this->em::create(
                    $manager->getConnection(),
                    $manager->getConfiguration()
                );
                continue;
            }
            $keyword_ar = new Keyword();
            $keyword_ar->setName($faker_ar->words(random_int(1, 3), true).'-'.$i.'ar');
            $keyword_ar->setLocale('ar');
            $keyword_ar->setMainKeyword($keyword_en);
            $manager->persist($keyword_ar);

            try {
                $manager->flush();

            }catch (UniqueConstraintViolationException $e) {
                $manager = $this->em::create(
                    $manager->getConnection(),
                    $manager->getConfiguration()
                );
                continue;
            }
        }


    }
}
