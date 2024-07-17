<?php

namespace App\Controller;

use App\Entity\Trainee;
use App\Form\TraineeType;
use App\Repository\TraineeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{_locale}/trainee')]
class TraineeController extends AbstractController
{
    #[Route('/', name: 'app_trainee_index', methods: ['GET'])]
    public function index(TraineeRepository $traineeRepository): Response
    {
        return $this->render('trainee/index.html.twig', [
            'trainees' => $traineeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_trainee_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $trainee = new Trainee();
        $form = $this->createForm(TraineeType::class, $trainee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($trainee);
            $entityManager->flush();

            return $this->redirectToRoute('app_trainee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trainee/new.html.twig', [
            'trainee' => $trainee,
            'form' => $form,
        ]);
    }

    #[Route('/{uuid}', name: 'app_trainee_show', methods: ['GET'])]
    public function show(TraineeRepository $traineeRepository, string $uuid): Response
    {
        return $this->render('trainee/show.html.twig', [
            'trainee' => $traineeRepository->findOneBy(['uuid' => $uuid]),
        ]);
    }

    #[Route('/{uuid}/edit', name: 'app_trainee_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, TraineeRepository $traineeRepository, string $uuid): Response
    {
        $trainee = $traineeRepository->findOneBy(['uuid' => $uuid]);
        $form = $this->createForm(TraineeType::class, $trainee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_trainee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trainee/edit.html.twig', [
            'trainee' => $trainee,
            'form' => $form,
        ]);
    }

    #[Route('/{uuid}', name: 'app_trainee_delete', methods: ['POST'])]
    public function delete(Request $request, TraineeRepository $traineeRepository, EntityManagerInterface $entityManager, string $uuid): Response
    {
        $trainee = $traineeRepository->findOneBy(['uuid' => $uuid]);
        if ($this->isCsrfTokenValid('delete' . $trainee->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($trainee);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_trainee_index', [], Response::HTTP_SEE_OTHER);
    }
}
