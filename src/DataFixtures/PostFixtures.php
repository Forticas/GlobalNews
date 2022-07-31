<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Category;
use App\Entity\Keyword;
use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PostFixtures extends Fixture  implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // get All authors where locale is en
        $authors_en = $manager->getRepository(Author::class)->findBy(['locale' => 'en']);
        // get All categories where locale is en
        $categories_en = $manager->getRepository(Category::class)->findBy(['locale' => 'en']);
        // get All keywords where locale is en
        $keywords_en = $manager->getRepository(Keyword::class)->findBy(['locale' => 'en']);
        // get All authors where locale is fr
        $authors_fr = $manager->getRepository(Author::class)->findBy(['locale' => 'fr']);
        // get All categories where locale is fr
        $categories_fr = $manager->getRepository(Category::class)->findBy(['locale' => 'fr']);
        // get All keywords where locale is fr
        $keywords_fr = $manager->getRepository(Keyword::class)->findBy(['locale' => 'fr']);
        // get All authors where locale is ar
        $authors_ar = $manager->getRepository(Author::class)->findBy(['locale' => 'ar']);
        // get All categories where locale is ar
        $categories_ar = $manager->getRepository(Category::class)->findBy(['locale' => 'ar']);
        // get All keywords where locale is ar
        $keywords_ar = $manager->getRepository(Keyword::class)->findBy(['locale' => 'ar']);

        $faker_en = Factory::create('en_US');
        $faker_fr = Factory::create('fr_FR');
        $faker_ar = Factory::create('ar_SA');
        /*
         * post content is HTML
         * generate 1000 posts with locale is en
         * and fr and ar other posts that main post is en
         * add one random author and one random category respecting the locale
         * add many random keywords for each post respecting the locale without duplicated keywords
         */
        for ($i = 0; $i < 1000; $i++) {

            $keywords_number = random_int(1, 10);

            $post_en = new Post();
            $post_en->setTitle($faker_en->sentence.'-'.$i);
            $post_en->setExcerpt($faker_en->text);
            $post_en->setContent($faker_en->randomHtml);
            $post_en->setLocale('en');
            $post_en->setAuthor($authors_en[array_rand($authors_en)]);
            $post_en->setCategory($categories_en[array_rand($categories_en)]);
            for ($j = 0; $j < $keywords_number; $j++) {

                $keyword = $keywords_en[array_rand($keywords_en)];
                if (!in_array($keyword, $post_en->getKeywords()->toArray(), true)) {
                    $post_en->addKeyword($keyword);
                }

            }
            $manager->persist($post_en);
            try {
                $manager->flush();

            }catch (UniqueConstraintViolationException $e) {
                if (!$manager->isOpen()) {
                    $manager = $manager::create(
                        $manager->getConnection(),
                        $manager->getConfiguration()
                    );
                }
                continue;
            }
            $post_fr = new Post();
            $post_fr->setTitle($faker_fr->sentence.'-'.$i);
            $post_fr->setExcerpt($faker_fr->text);
            $post_fr->setContent($faker_fr->randomHtml);
            $post_fr->setLocale('fr');
            $post_fr->setMainPost($post_en);
            $post_fr->setAuthor($authors_fr[array_rand($authors_fr)]);
            $post_fr->setCategory($categories_fr[array_rand($categories_fr)]);
            for ($j = 0; $j < $keywords_number; $j++) {

                $keyword = $keywords_fr[array_rand($keywords_fr)];
                if (!in_array($keyword, $post_fr->getKeywords()->toArray(), true)) {
                    $post_fr->addKeyword($keyword);
                }

            }
            $manager->persist($post_fr);
            try {
                $manager->flush();

            }catch (UniqueConstraintViolationException $e) {
                if (!$manager->isOpen()) {
                    $manager = $manager::create(
                        $manager->getConnection(),
                        $manager->getConfiguration()
                    );
                }
                continue;
            }

            $post_ar = new Post();
            $post_ar->setTitle($faker_ar->sentence.'-'.$i);
            $post_ar->setExcerpt($faker_ar->text);
            $post_ar->setContent($faker_ar->randomHtml);
            $post_ar->setLocale('ar');
            $post_ar->setMainPost($post_en);
            $post_ar->setAuthor($authors_ar[array_rand($authors_ar)]);
            $post_ar->setCategory($categories_ar[array_rand($categories_ar)]);
            for ($j = 0; $j < $keywords_number; $j++) {

                $keyword = $keywords_ar[array_rand($keywords_ar)];
                if (!in_array($keyword, $post_ar->getKeywords()->toArray(), true)) {
                    $post_ar->addKeyword($keyword);
                }

            }
            $manager->persist($post_ar);


            try {
                $manager->flush();

            }catch (UniqueConstraintViolationException $e) {
                if (!$manager->isOpen()) {
                    $manager = $manager::create(
                        $manager->getConnection(),
                        $manager->getConfiguration()
                    );
                }
                continue;
            }
        }
    }

    public function getDependencies(): array
    {
        return [
            AuthorFixtures::class,
            CategoryFixtures::class,
            KeywordFixtures::class,
        ];
    }
}
