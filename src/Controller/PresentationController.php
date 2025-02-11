<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PresentationController extends AbstractController
{
    #[Route('/{_locale}/', name: 'app_presentation', methods: ['GET'])]
    public function index(): Response
    {
        if($this->getUser() !== null){
            return $this->redirectToRoute('app_home');
        }
        return $this->render('presentation/index.html.twig');
    }
} 