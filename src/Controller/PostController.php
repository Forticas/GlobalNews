<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class PostController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private CacheInterface $cache
    )
    {
    }

    #[Route('/{category_slug}/{post_slug}', name: 'app_post_show')]
    public function showPost(string $category_slug, string $post_slug, Request $request): Response
    {
        $post = $this->cache->get('category_' . $category_slug.'post_' . $post_slug, function () use ($post_slug, $request) {
            return $this->em->getRepository(Post::class)
                ->findOneBy(['slug' => $post_slug, 'locale' => $request->getLocale()]);
        });
        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }
        return $this->render('post/show-post.html.twig', [
            'post' => $post,
        ]);
    }

    // show posts by category slug with pagination
    #[Route('/{category_slug}', name: 'app_posts_by_category')]
    public function showPostsByCategory(string $category_slug, Request $request): Response
    {

        $page = $request->query->getInt('page', 1);
        // get one category by slug and save in cache without expiration time
        $category = $this->cache->get('category_' . $category_slug, function () use ($category_slug) {
            return $this->em->getRepository(Category::class)
                ->findOneBy(['slug' => $category_slug]);
        });

        $posts = $this->cache->get('posts_by_category_' . $category_slug.'_page_' . $page, function (ItemInterface $item) use ($category, $page, $request) {
            $item->expiresAfter(3600);
            //get category by slug

            return $this->em->getRepository(Post::class)
                ->findBy(['category' => $category, 'locale' => $request->getLocale()], [], 10, ($page-1)*10);
        });

        //get page number with 10 posts per page
        $pages = $this->cache->get('category_' . $category_slug.'_page_number', function (ItemInterface $item) use ($category,$request) {
            $item->expiresAfter(3600);
            return $this->em->getRepository(Post::class)
                ->count(['category' => $category, 'locale' => $request->getLocale()]) / 10;
        });
        return $this->render('post/posts-by-category.html.twig', [
            'posts' => $posts,
            'pages' => $pages,
        ]);
    }

    // show posts by keyword slug with pagination
    #[Route('/keyword/{keyword_slug}', name: 'app_posts_by_keyword')]
    public function showPostsByKeyword(string $keyword_slug, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $posts = $this->cache->get('keyword_' . $keyword_slug.'_page_' . $page, function (ItemInterface $item) use ($keyword_slug, $page, $request) {
            $item->expiresAfter(3600);
            return $this->em->getRepository(Post::class)
                ->findBy(['keywords' => $keyword_slug, 'locale' => $request->getLocale()], [], 10, ($page-1)*10);
        });
        //get page number with 10 posts per page
        $pages = $this->cache->get('keyword_' . $keyword_slug.'_page_number', function (ItemInterface $item) use ($keyword_slug,$request) {
            $item->expiresAfter(3600);
            return $this->em->getRepository(Post::class)
                    // TODO à verifier car la relation est ManyToMany
                ->count(['keywords' => $keyword_slug, 'locale' => $request->getLocale()]) / 10;
        });
        return $this->render('post/posts-by-keyword.html.twig', [
            'posts' => $posts,
            'pages' => $pages,
        ]);
    }
}
