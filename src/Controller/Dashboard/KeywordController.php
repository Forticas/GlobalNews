<?php

namespace App\Controller\Dashboard;

use App\Entity\Keyword;
use App\Form\KeywordType;
use App\Repository\KeywordRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[Route('/dashboard/keyword')]
class KeywordController extends AbstractController
{
    public function __construct(
        private CacheInterface $cache
    )
    {
    }


    #[Route('/', name: 'app_keyword_index', methods: ['GET'])]
    public function index(KeywordRepository $keywordRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $keywords = $this->cache->get('keywords_'.$page, function (ItemInterface $item) use ($keywordRepository, $page) {
            $item->expiresAfter(3600);
            return $keywordRepository->findBy([], [], 10, ($page-1)*10);
        });

        //get page number with 10 keywords per page
        $pages = $this->cache->get('keyword_page_number', function (ItemInterface $item) use ($keywordRepository) {
            $item->expiresAfter(3600);
            return $keywordRepository->count([]) / 10;
        });
        return $this->render('dashboard/keyword/index.html.twig', [
            'keywords' => $keywords,
            'pages' => $pages,
        ]);
    }

    #[Route('/new', name: 'app_keyword_new', methods: ['GET', 'POST'])]
    public function new(Request $request, KeywordRepository $keywordRepository): Response
    {
        $keyword = new Keyword();
        $form = $this->createForm(KeywordType::class, $keyword);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $keywordRepository->add($keyword, true);

            return $this->redirectToRoute('app_keyword_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dashboard/keyword/new.html.twig', [
            'keyword' => $keyword,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_keyword_show', methods: ['GET'])]
    public function show(Keyword $keyword): Response
    {
        return $this->render('dashboard/keyword/show.html.twig', [
            'keyword' => $keyword,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_keyword_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Keyword $keyword, KeywordRepository $keywordRepository): Response
    {
        $form = $this->createForm(KeywordType::class, $keyword);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $keywordRepository->add($keyword, true);

            return $this->redirectToRoute('app_keyword_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dashboard/keyword/edit.html.twig', [
            'keyword' => $keyword,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_keyword_delete', methods: ['POST'])]
    public function delete(Request $request, Keyword $keyword, KeywordRepository $keywordRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$keyword->getId(), $request->request->get('_token'))) {
            $keywordRepository->remove($keyword, true);
        }

        return $this->redirectToRoute('app_keyword_index', [], Response::HTTP_SEE_OTHER);
    }
}
