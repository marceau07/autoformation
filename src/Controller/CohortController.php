<?php

namespace App\Controller;

use App\Entity\Cohort;
use App\Form\CohortType;
use App\Repository\CohortRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{_locale}/cohort')]
class CohortController extends AbstractController
{
    #[Route('/', name: 'app_cohort_index', methods: ['GET'])]
    public function index(CohortRepository $cohortRepository): Response
    {
        return $this->render('cohort/index.html.twig', [
            'cohorts' => $cohortRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_cohort_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cohort = new Cohort();
        $form = $this->createForm(CohortType::class, $cohort);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cohort);
            $entityManager->flush();

            return $this->redirectToRoute('app_cohort_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cohort/new.html.twig', [
            'cohort' => $cohort,
            'form' => $form,
        ]);
    }

    #[Route('/{uuid}', name: 'app_cohort_show', methods: ['GET'])]
    public function show(CohortRepository $cohortRepository, string $uuid): Response
    {
        return $this->render('cohort/show.html.twig', [
            'cohort' => $cohortRepository->findOneBy(['uuid' => $uuid]),
        ]);
    }

    #[Route('/{uuid}/edit', name: 'app_cohort_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CohortRepository $cohortRepository, EntityManagerInterface $entityManager, string $uuid): Response
    {
        $cohort = $cohortRepository->findOneBy(['uuid' => $uuid]);
        $form = $this->createForm(CohortType::class, $cohort);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cohort_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cohort/edit.html.twig', [
            'cohort' => $cohort,
            'form' => $form,
        ]);
    }

    #[Route('/{uuid}', name: 'app_cohort_delete', methods: ['POST'])]
    public function delete(Request $request, CohortRepository $cohortRepository, EntityManagerInterface $entityManager, string $uuid): Response
    {
        $cohort = $cohortRepository->findOneBy(['uuid' => $uuid]);
        if ($this->isCsrfTokenValid('delete' . $cohort->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($cohort);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cohort_index', [], Response::HTTP_SEE_OTHER);
    }
}
