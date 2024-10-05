<?php

namespace App\Controller;

use App\Entity\QuizRow;
use App\Form\QuizRowType;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_TRAINER')]
#[Route('/{_locale}/quiz-row')]
final class QuizRowController extends AbstractController
{
    #[Route(name: 'app_quiz_row_index', methods: ['GET'])]
    public function index(QuizRepository $quizRepository): Response
    {
        return $this->render('quiz_row/index.html.twig', [
            'quiz_rows' => $quizRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_quiz_row_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $quizRow = new QuizRow();
        $form = $this->createForm(QuizRowType::class, $quizRow);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($quizRow);
            $entityManager->flush();

            return $this->redirectToRoute('app_quiz_row_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('quiz_row/new.html.twig', [
            'quiz_row' => $quizRow,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_quiz_row_show', methods: ['GET'])]
    public function show(QuizRow $quizRow): Response
    {
        return $this->render('quiz_row/show.html.twig', [
            'quiz_row' => $quizRow,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_quiz_row_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, QuizRow $quizRow, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuizRowType::class, $quizRow);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_quiz_row_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('quiz_row/edit.html.twig', [
            'quiz_row' => $quizRow,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_quiz_row_delete', methods: ['POST'])]
    public function delete(Request $request, QuizRow $quizRow, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $quizRow->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($quizRow);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_quiz_row_index', [], Response::HTTP_SEE_OTHER);
    }
}
