<?php

namespace App\Controller;

use App\Repository\ExportParameterRepository;
use App\Repository\TrainerRepository;
use App\Service\ExcelExporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/{_locale}/trainer')]
class TrainerController extends AbstractController
{
    #[isGranted('ROLE_ADMIN')]
    #[Route('/export/', name: 'app_trainer_export', methods: ['GET'], priority: 1)]
    public function export(Request $request, ExcelExporter $excelExporter, TrainerRepository $trainerRepository, ExportParameterRepository $parameter): Response
    {
        $formFields = $request->query->all('form_fields');
        if (!is_array($formFields)) {
            $formFields = (array)[$formFields];
        }

        $data = [];
        $i = 0;
        foreach ($trainerRepository->findAll() as $trainer) {
            foreach ($formFields as $field) {
                $data[$i][] = $trainer->{'get' . ucfirst($field)}();
            }
            $i++;
        }

        // Utiliser le service pour générer le fichier Excel
        return $excelExporter->exportData('trainer_list', json_decode($parameter->findOneBy(['dtype' => 'trainer'])->getField(), true), $data, $formFields);
    }
}
