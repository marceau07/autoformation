<?php

namespace App\Controller;

use App\Entity\Internship;
use App\Form\InternshipType;
use App\Repository\ExportParameterRepository;
use App\Repository\InternshipRepository;
use App\Repository\NotificationRepository;
use App\Repository\TraineeInternshipRepository;
use App\Service\ExcelExporter;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV7;

#[IsGranted('ROLE_TRAINER')]
#[Route('/{_locale}/internship')]
class InternshipController extends AbstractController
{
    // TODO: Prendre des portions de code ici pour les mettre dans le controller de l'admin
    // #[Route('/new', name: 'app_internship_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, EntityManagerInterface $entityManager, NotificationRepository $notificationRepository, Filesystem $filesystem): Response
    // {
    //     $internship = new Internship();
    //     $form = $this->createForm(InternshipType::class, $internship);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $uuid = new UuidV7();
    //         $entityManager->persist($internship);
    //         $entityManager->flush();
    //         try {
    //             if (!$filesystem->exists($this->getParameter('internships_directory') . "/" . $internship->getTrainee()->getUsername())) {
    //                 $filesystem->mkdir($this->getParameter('internships_directory') . "/" . $internship->getTrainee()->getUsername());
    //             }
    //             $filesystem->copy($this->getParameter('internships_directory') . "/tmp/Convention_de_stage_" . strtoupper($internship->getTrainee()->getLastName()) . "_" . ucfirst($internship->getTrainee()->getFirstName()) . ".pdf", $this->getParameter('internships_directory') . "/" . $internship->getTrainee()->getUsername() . "/Convention_de_stage_" . $uuid . ".pdf");
    //             $filesystem->remove($this->getParameter('internships_directory') . "/tmp/Convention_de_stage_" . strtoupper($internship->getTrainee()->getLastName()) . "_" . ucfirst($internship->getTrainee()->getFirstName()) . ".pdf");
    //             $this->addFlash('info', 'Convention de stage enregistrée avec succès !');
    //             $documents = json_decode($internship->getTrainee()->getDocuments(), true);
    //             $documents['internships'][0]['internship_id'] = $internship->getId();
    //             $documents['internships'][0]['agreement'] = 1;
    //             $documents['internships'][0]['agreement_link'] = $uuid;
    //             $internship->getTrainee()->setDocuments(json_encode($documents));
    //             $entityManager->persist($internship->getTrainee());
    //             $entityManager->flush();

    //             $notificationRepository->deleteANotification('new_internship', $internship->getTrainee()->getCohort()->getTrainer()->getId(), $internship->getTrainee()->getUsername(), null);
    //         } catch (Exception $e) {
    //             $this->addFlash('danger', "Erreur lors de la copie de la convention de stage..." . $e->getMessage());
    //         }
    //         return $this->redirectToRoute('app_internship_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('internship/new.html.twig', [
    //         'internship' => $internship,
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/export/', name: 'app_internship_export', methods: ['GET'], priority: 1)]
    public function export(Request $request, ExcelExporter $excelExporter, InternshipRepository $internshipRepository, ExportParameterRepository $parameter): Response
    {
        $formFields = $request->query->all('form_fields');
        if (!is_array($formFields)) {
            $formFields = (array)[$formFields];
        }

        $data = [];
        $i = 0;
        foreach ($internshipRepository->findAll() as $internship) {
            foreach ($formFields as $field) {
                $data[$i][] = $internship->{'get' . ucfirst(str_replace('_', '', $field))}();
            }
            $i++;
        }

        // Utiliser le service pour générer le fichier Excel
        return $excelExporter->exportData('internship_list', json_decode($parameter->findOneBy(['dtype' => 'internship'])->getField(), true), $data, $formFields);
    }

    #[Route('/asking-elements/{traineeInternshipId}', 'app_internships_asking_elements', methods: ['GET'])]
    public function askingElements(int $traineeInternshipId, TraineeInternshipRepository $traineeInternshipRepository, Request $request, MailerInterface $mailer): Response
    {
        $traineeInternship = $traineeInternshipRepository->find($traineeInternshipId);

        $author = $traineeInternship->getInternship()->getTrainee()->getCohort()->getTrainer();
        $trainee = $traineeInternship->getInternship()->getTrainee();
        $tutor = $traineeInternship->getInternship();
        $email = (new Email())
            ->from('no-reply@marceau-rodrigues.fr')
            ->subject('Demande de documents de stage');
        if ($_ENV['APP_ENV'] === 'prod') {
            $email->to($tutor->getTutorEmail());
        } elseif ($_ENV['APP_ENV'] === 'preprod' || $_ENV['APP_ENV'] === 'dev') {
            $email->to('contact@marceau-rodrigues.fr');
        }
        $missingDocuments = '';
        if ($traineeInternship->isAgreement() === false) {
            $missingDocuments .= '<li><b>Convention de stage:</b><i>Ce document est essentiel, il permet d\'établir un contrat en vous et nous pour montrer que le stagiaire sera avec vous sur un temps donné.</i></li>';
        }
        if ($traineeInternship->isCertificate() === false) {
            $missingDocuments .= '<li><b>Attestation de stage:</b><i>Veuillez nous fournir une copie signée de votre attestation de stage. Assurez vous que tous les détails, sont correctement remplis.</i></li>';
        }
        if ($traineeInternship->isEvaluation() === false) {
            $missingDocuments .= '<li><b>Évaluation de stage:</b><i>Afin de pouvoir avoir un retour sur la prestation du stagiaire, il est important que ce document nous soit retourné.</i></li>';
        }
        $email->html($this->renderView('_emails/MAIL_DEMANDE.html.twig', [
            'NOM_TUTEUR' => $tutor->getTutorEmail(),
            'PRENOM_NOM_STAGIAIRE' => ucfirst($trainee->getFirstName()) . ' ' . strtoupper($trainee->getLastName()),
            'LISTE_DOCUMENTS' => $missingDocuments,
            'CARTE_PRENOM' => $author->getFirstName(),
            'CARTE_NOM' => $author->getLastName(),
            'CARTE_ROLE' => $author->getRole(),
            'CARTE_LOGO_SECTEUR' => $author->getSector()->getLogo(),
            'CARTE_LIENS' => '',
            'CARTE_NUMEROS' => '',
            'CARTE_ADRESSE' => '',
        ]));

        $mailer->send($email);
        $this->addFlash('notice', 'Le mail est partie');

        return $this->redirectToRoute('app_home');
    }
}
