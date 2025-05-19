<?php

namespace App\Controller;

use App\Repository\ExportParameterRepository;
use App\Repository\CoordinatorRepository;
use App\Service\ExcelExporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[isGranted('ROLE_ADMIN')]
#[Route('/{_locale}/coordinator')]
class CoordinatorController extends AbstractController
{
    #[Route('/export/', name: 'app_coordinator_export', methods: ['GET'], priority: 1)]
    public function export(Request $request, ExcelExporter $excelExporter, CoordinatorRepository $coordinatorRepository, ExportParameterRepository $parameter): Response
    {
        $formFields = $request->query->all('form_fields');
        if (!is_array($formFields)) {
            $formFields = (array)[$formFields];
        }

        $data = [];
        $i = 0;
        foreach ($coordinatorRepository->findAll() as $coordinator) {
            foreach ($formFields as $field) {
                $data[$i][] = $coordinator->{'get' . ucfirst($field)}();
            }
            $i++;
        }

        // Utiliser le service pour générer le fichier Excel
        return $excelExporter->exportData('coordinator_list', json_decode($parameter->findOneBy(['dtype' => 'coordinator'])->getField(), true), $data, $formFields);
    }
}
