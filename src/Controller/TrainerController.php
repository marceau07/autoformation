<?php

namespace App\Controller;

use App\Entity\Trainer;
use App\Form\TrainerType;
use App\Repository\TrainerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_TRAINER')]
#[Route('/{_locale}/trainer')]
class TrainerController extends AbstractController
{
    #[Route('/', name: 'app_trainer_index', methods: ['GET'])]
    public function index(TrainerRepository $trainerRepository): Response
    {
        return $this->render('trainer/index.html.twig', [
            'trainers' => $trainerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_trainer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $trainer = new Trainer();
        $form = $this->createForm(TrainerType::class, $trainer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($trainer);
            $entityManager->flush();

            return $this->redirectToRoute('app_trainer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trainer/new.html.twig', [
            'trainer' => $trainer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_trainer_show', methods: ['GET'])]
    public function show(Trainer $trainer): Response
    {
        return $this->render('trainer/show.html.twig', [
            'trainer' => $trainer,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_trainer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trainer $trainer, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TrainerType::class, $trainer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_trainer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trainer/edit.html.twig', [
            'trainer' => $trainer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_trainer_delete', methods: ['POST'])]
    public function delete(Request $request, Trainer $trainer, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $trainer->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($trainer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_trainer_index', [], Response::HTTP_SEE_OTHER);
    }
}
