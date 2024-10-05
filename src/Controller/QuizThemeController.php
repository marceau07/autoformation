<?php

namespace App\Controller;

use App\Entity\QuizTheme;
use App\Form\QuizThemeType;
use App\Repository\QuizThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_TRAINER')]
#[Route('/{_locale}/quiz-theme')]
final class QuizThemeController extends AbstractController
{
    #[Route(name: 'app_quiz_theme_index', methods: ['GET'])]
    public function index(QuizThemeRepository $quizThemeRepository): Response
    {
        return $this->render('quiz_theme/index.html.twig', [
            'quiz_themes' => $quizThemeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_quiz_theme_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $quizTheme = new QuizTheme();
        $form = $this->createForm(QuizThemeType::class, $quizTheme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $request->files->get('quiz_theme')['illustration'];
            if ($file) {
                if ($file->getSize() <= 52428800) {
                    if ($file->getMimeType() == 'image/jpeg' || $file->getMimeType() == 'image/png' || $file->getMimeType() == 'image/gif' || $file->getMimeType() == 'video/mp4') {
                        $nomFichier = strtolower($quizTheme->getName()) . '.' . $file->guessExtension();
                        try {
                            $file->move($this->getParameter('quizzes_directory'), $nomFichier);
                            $quizTheme->setIllustration($nomFichier);
                            $this->addFlash('info', 'Thème créé avec succès');
                        } catch (FileException $e) {
                            $this->addFlash('danger', 'Erreur lors de l\'ajout de l\'illustration: ' . $e->getMessage());
                        }
                    }
                }
            }
            $entityManager->persist($quizTheme);
            $entityManager->flush();

            return $this->redirectToRoute('app_quiz_theme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('quiz_theme/new.html.twig', [
            'quiz_theme' => $quizTheme,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_quiz_theme_show', methods: ['GET'])]
    public function show(QuizTheme $quizTheme): Response
    {
        return $this->render('quiz_theme/show.html.twig', [
            'quiz_theme' => $quizTheme,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_quiz_theme_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, QuizTheme $quizTheme, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuizThemeType::class, $quizTheme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_quiz_theme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('quiz_theme/edit.html.twig', [
            'quiz_theme' => $quizTheme,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_quiz_theme_delete', methods: ['POST'])]
    public function delete(Request $request, QuizTheme $quizTheme, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quizTheme->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($quizTheme);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_quiz_theme_index', [], Response::HTTP_SEE_OTHER);
    }
}
