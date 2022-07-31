<?php

namespace App\DataFixtures;

use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AuthorFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker_en = Factory::create('en_US');
        $faker_fr = Factory::create('fr_FR');
        $faker_ar = Factory::create('ar_SA');
        for ($i = 0; $i < 10; $i++) {
            $author_en = new Author();
            $author_en->setName($faker_en->name.'-'.$i);
            $author_en->setDescription($faker_en->text);
            $manager->persist($author_en);

            $author_fr = new Author();
            $author_fr->setName($faker_fr->name.'-'.$i);
            $author_fr->setDescription($faker_fr->text);
            $author_fr->setLocale('fr');
            $author_fr->setMainAuthor($author_en);
            $manager->persist($author_fr);

            $author_ar = new Author();
            $author_ar->setName($faker_ar->name.'-'.$i);
            $author_ar->setDescription($faker_ar->text);
            $author_ar->setLocale('ar');
            $author_ar->setMainAuthor($author_en);
            $manager->persist($author_ar);

            try {
                $manager->flush();

            }catch (UniqueConstraintViolationException $e) {

            }
        }


    }
}
