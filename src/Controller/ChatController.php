<?php

namespace App\Controller;

use App\Form\MessageType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ChatController extends AbstractController
{
    #[Route('/chat', name: 'app_chat')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(MessageType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            return new Response($this->renderView('chat/message.stream.html.twig', ['message' => $form->getData()]));
        }

        return $this->render('chat/index.html.twig', [
            'form' => $form,
        ]);
    }
}
