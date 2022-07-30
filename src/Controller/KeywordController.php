<?php

namespace App\Controller;

use App\Entity\Keyword;
use App\Form\KeywordType;
use App\Repository\KeywordRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard/keyword')]
class KeywordController extends AbstractController
{
    #[Route('/', name: 'app_keyword_index', methods: ['GET'])]
    public function index(KeywordRepository $keywordRepository): Response
    {
        return $this->render('keyword/index.html.twig', [
            'keywords' => $keywordRepository->findAll(),
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

        return $this->renderForm('keyword/new.html.twig', [
            'keyword' => $keyword,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_keyword_show', methods: ['GET'])]
    public function show(Keyword $keyword): Response
    {
        return $this->render('keyword/show.html.twig', [
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

        return $this->renderForm('keyword/edit.html.twig', [
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
