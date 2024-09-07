<?php

namespace App\Controller;

use App\Entity\Internship;
use App\Form\InternshipType;
use App\Repository\InternshipRepository;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV7;

#[IsGranted('ROLE_TRAINER')]
#[Route('/{_locale}/internship')]
class InternshipController extends AbstractController
{
    #[Route('/', name: 'app_internship_index', methods: ['GET'])]
    public function index(InternshipRepository $internshipRepository): Response
    {
        return $this->render('internship/index.html.twig', [
            'internships' => $internshipRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_internship_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, NotificationRepository $notificationRepository, Filesystem $filesystem): Response
    {
        $internship = new Internship();
        $form = $this->createForm(InternshipType::class, $internship);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uuid = new UuidV7();
            $entityManager->persist($internship);
            $entityManager->flush();
            try {
                if(!$filesystem->exists($this->getParameter('internships_directory') . "/" . $internship->getTrainee()->getUsername())) {
                    $filesystem->mkdir($this->getParameter('internships_directory') . "/" . $internship->getTrainee()->getUsername());
                }
                $filesystem->copy($this->getParameter('internships_directory') . "/tmp/Convention_de_stage_" . strtoupper($internship->getTrainee()->getLastName()) . "_" . ucfirst($internship->getTrainee()->getFirstName()) . ".pdf", $this->getParameter('internships_directory') . "/" . $internship->getTrainee()->getUsername() . "/Convention_de_stage_" . $uuid . ".pdf");
                $filesystem->remove($this->getParameter('internships_directory') . "/tmp/Convention_de_stage_" . strtoupper($internship->getTrainee()->getLastName()) . "_" . ucfirst($internship->getTrainee()->getFirstName()) . ".pdf");
                $this->addFlash('info', 'Convention de stage enregistrée avec succès !');
                $documents = json_decode($internship->getTrainee()->getDocuments(), true);
                $documents['internships'][0]['internship_id'] = $internship->getId();
                $documents['internships'][0]['agreement'] = 1;
                $documents['internships'][0]['agreement_link'] = $uuid;
                $internship->getTrainee()->setDocuments(json_encode($documents));
                $entityManager->persist($internship->getTrainee());
                $entityManager->flush();

                $notificationRepository->deleteANotification($internship->getTrainee()->getUsername(), null, 'new_internship', $internship->getTrainee()->getCohort()->getTrainer()->getId());
            } catch (Exception $e) {
                $this->addFlash('danger', "Erreur lors de la copie de la convention de stage..." . $e->getMessage());
            }
            return $this->redirectToRoute('app_internship_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('internship/new.html.twig', [
            'internship' => $internship,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_internship_show', methods: ['GET'])]
    public function show(Internship $internship): Response
    {
        return $this->render('internship/show.html.twig', [
            'internship' => $internship,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_internship_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Internship $internship, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InternshipType::class, $internship);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_internship_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('internship/edit.html.twig', [
            'internship' => $internship,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_internship_delete', methods: ['POST'])]
    public function delete(Request $request, Internship $internship, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$internship->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($internship);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_internship_index', [], Response::HTTP_SEE_OTHER);
    }
}
