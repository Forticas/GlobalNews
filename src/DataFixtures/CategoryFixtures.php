<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CategoryFixtures extends Fixture
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
        // generate 10 categories and fr and ar other categories where main category is en
        for ($i = 0; $i < 10; $i++) {
            $category_en = new Category();
            $category_en->setName($faker_en->words(random_int(1,3), true).'-'.$i);
            $category_en->setDescription($faker_en->text);
            $manager->persist($category_en);

            $category_fr = new Category();
            $category_fr->setName($faker_fr->words(random_int(1,3), true).'-'.$i);
            $category_fr->setDescription($faker_fr->text);
            $category_fr->setLocale('fr');
            $category_fr->setMainCategory($category_en);
            $manager->persist($category_fr);

            $category_ar = new Category();
            $category_ar->setName($faker_ar->words(random_int(1,3), true).'-'.$i);
            $category_ar->setDescription($faker_ar->text);
            $category_ar->setLocale('ar');
            $category_ar->setMainCategory($category_en);
            $manager->persist($category_ar);

            try {
                $manager->flush();

            }catch (UniqueConstraintViolationException $e) {
                $manager = $this->em::create(
                    $manager->getConnection(),
                    $manager->getConfiguration()
                );
            }
        }

    }
}
