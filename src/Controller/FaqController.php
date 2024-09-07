<?php

namespace App\Controller;

use App\Entity\Faq;
use App\Form\FaqType;
use App\Repository\FaqRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_TRAINER')]
#[Route('/{_locale}/faq')]
class FaqController extends AbstractController
{
    #[Route('/', name: 'app_faq_index', methods: ['GET'])]
    public function index(FaqRepository $faqRepository): Response
    {
        return $this->render('faq/index.html.twig', [
            'faqs' => $faqRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_faq_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $faq = new Faq();
        $form = $this->createForm(FaqType::class, $faq);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($faq);
            $entityManager->flush();

            return $this->redirectToRoute('app_faq_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('faq/new.html.twig', [
            'faq' => $faq,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_faq_show', methods: ['GET'])]
    public function show(Faq $faq): Response
    {
        return $this->render('faq/show.html.twig', [
            'faq' => $faq,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_faq_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Faq $faq, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FaqType::class, $faq);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_faq_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('faq/edit.html.twig', [
            'faq' => $faq,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_faq_delete', methods: ['POST'])]
    public function delete(Request $request, Faq $faq, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $faq->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($faq);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_faq_index', [], Response::HTTP_SEE_OTHER);
    }
}
