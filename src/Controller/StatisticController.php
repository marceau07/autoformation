<?php

namespace App\Controller;

use App\Repository\CohortRepository;
use App\Repository\TraineeRepository;
use App\Repository\TrainerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_TRAINER')]
#[Route('/{_locale}/statistic')]
class StatisticController extends AbstractController
{
    #[Route('/', name: 'app_statistic_index')]
    public function index(TraineeRepository $traineeRepository, TrainerRepository $trainerRepository, CohortRepository $cohortRepository): Response
    {
        $agreement = $ratio_agreement = $certificate = $ratio_certificate = $evaluation = $ratio_evaluation = 0;
        $totalAgreement = $totalCertificate = $totalEvaluation = 0;
        // dump($trainerRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()])->getCohorts());
        foreach ($traineeRepository->findBy(['cohort' => $trainerRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()])]) as $trainee) {
            if ($trainee->getDocuments() !== null && !empty($trainee->getDocuments())) {
                foreach (json_decode($trainee->getDocuments(), true)['internships'] as $internship) {
                    if ($internship['agreement'] == 1) {
                        $agreement++;
                        $totalAgreement++;
                    } elseif ($internship['agreement'] != 1) {
                        $totalAgreement++;
                    }

                    if ($internship['certificate'] == 1) {
                        $certificate++;
                        $totalCertificate++;
                    } elseif ($internship['certificate'] != 1) {
                        $totalCertificate++;
                    }

                    if ($internship['evaluation'] == 1) {
                        $evaluation++;
                        $totalEvaluation++;
                    } elseif ($internship['evaluation'] != 1) {
                        $totalEvaluation++;
                    }
                }
            }
        }
        if (!empty($totalAgreement)) $ratio_agreement = $agreement / $totalAgreement;
        if (!empty($totalCertificate)) $ratio_certificate = $certificate / $totalCertificate;
        if (!empty($totalEvaluation)) $ratio_evaluation = $evaluation / $totalEvaluation;

        $ratio_agreement_label = $ratio_certificate_label = $ratio_evaluation_label = "sur l'ensemble des cohortes passées et actuelles";

        return $this->render('statistic/index.html.twig', [
            'ratio_agreement' => $ratio_agreement,
            'ratio_agreement_label' => $ratio_agreement_label,
            'ratio_certificate' => $ratio_certificate,
            'ratio_certificate_label' => $ratio_certificate_label,
            'ratio_evaluation' => $ratio_evaluation,
            'ratio_evaluation_label' => $ratio_evaluation_label,
        ]);
    }
}
