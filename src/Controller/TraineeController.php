<?php

namespace App\Controller;

use App\Repository\ExportParameterRepository;
use App\Repository\TraineeRepository;
use App\Service\ExcelExporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_TRAINER')]
#[Route('/{_locale}/trainee')]
class TraineeController extends AbstractController
{
    #[Route('/export/', name: 'app_trainee_export', methods: ['GET'], priority: 1)]
    public function export(Request $request, ExcelExporter $excelExporter, TraineeRepository $traineeRepository, ExportParameterRepository $parameter): Response
    {
        $formFields = $request->query->all('form_fields');
        if (!is_array($formFields)) {
            $formFields = (array)[$formFields];
        }

        $data = [];
        $i = 0;
        foreach ($traineeRepository->findAll() as $trainee) {
            foreach ($formFields as $field) {
                $data[$i][] = $trainee->{'get' . ucfirst($field)}();
            }
            $i++;
        }

        // Utiliser le service pour générer le fichier Excel
        return $excelExporter->exportData('trainee_list', json_decode($parameter->findOneBy(['dtype' => 'trainee'])->getField(), true), $data, $formFields);
    }
}
