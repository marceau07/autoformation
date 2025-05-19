<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/{_locale}/quiz')]
final class QuizController extends AbstractController
{
    #[Route('/redirect/{uuid}', name: 'app_quiz_redirection', methods: ['GET'])]
    public function redirection(string $uuid): Response
    {
        return $this->render('quiz/redirect.html.twig', [
            'uuid' => $uuid,
        ]);
    }
}
