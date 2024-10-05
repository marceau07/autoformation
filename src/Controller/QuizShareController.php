<?php

namespace App\Controller;

use App\Entity\QuizShare;
use App\Form\QuizShareType;
use App\Repository\QuizShareRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_TRAINER')]
#[Route('/{_locale}/quiz-share')]
final class QuizShareController extends AbstractController
{
    #[Route(name: 'app_quiz_share_index', methods: ['GET'])]
    public function index(QuizShareRepository $quizShareRepository): Response
    {
        return $this->render('quiz_share/index.html.twig', [
            'quiz_shares' => $quizShareRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_quiz_share_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $quizShare = new QuizShare();
        $form = $this->createForm(QuizShareType::class, $quizShare);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($quizShare);
            $entityManager->flush();

            return $this->redirectToRoute('app_quiz_share_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('quiz_share/new.html.twig', [
            'quiz_share' => $quizShare,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_quiz_share_show', methods: ['GET'])]
    public function show(QuizShare $quizShare): Response
    {
        return $this->render('quiz_share/show.html.twig', [
            'quiz_share' => $quizShare,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_quiz_share_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, QuizShare $quizShare, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuizShareType::class, $quizShare);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_quiz_share_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('quiz_share/edit.html.twig', [
            'quiz_share' => $quizShare,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_quiz_share_delete', methods: ['POST'])]
    public function delete(Request $request, QuizShare $quizShare, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quizShare->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($quizShare);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_quiz_share_index', [], Response::HTTP_SEE_OTHER);
    }
}
