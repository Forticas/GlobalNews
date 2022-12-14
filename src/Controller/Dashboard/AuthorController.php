<?php

namespace App\Controller\Dashboard;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard/author')]
class AuthorController extends AbstractController
{
    #[Route('/', name: 'dashboard_author_index', methods: ['GET'])]
    public function index(AuthorRepository $authorRepository): Response
    {
        return $this->render('dashboard/author/index.html.twig', [
            'authors' => $authorRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'dashboard_author_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AuthorRepository $authorRepository): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $authorRepository->add($author, true);

            return $this->redirectToRoute('dashboard_author_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dashboard/author/new.html.twig', [
            'author' => $author,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'dashboard_author_show', methods: ['GET'])]
    public function show(Author $author): Response
    {
        return $this->render('dashboard/author/show.html.twig', [
            'author' => $author,
        ]);
    }

    #[Route('/{id}/edit', name: 'dashboard_author_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Author $author, AuthorRepository $authorRepository): Response
    {
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $authorRepository->add($author, true);

            return $this->redirectToRoute('dashboard_author_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dashboard/author/edit.html.twig', [
            'author' => $author,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'dashboard_author_delete', methods: ['POST'])]
    public function delete(Request $request, Author $author, AuthorRepository $authorRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$author->getId(), $request->request->get('_token'))) {
            $authorRepository->remove($author, true);
        }

        return $this->redirectToRoute('dashboard_author_index', [], Response::HTTP_SEE_OTHER);
    }
}
