<?php

namespace App\Controller;

use App\Repository\ExportParameterRepository;
use App\Repository\ProspectRepository;
use App\Service\ExcelExporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_TRAINER')]
#[Route('/{_locale}/prospect')]
class ProspectController extends AbstractController
{
    #[Route('/export/', name: 'app_prospect_export', methods: ['GET'], priority: 1)]
    public function export(Request $request, ExcelExporter $excelExporter, ProspectRepository $prospectRepository, ExportParameterRepository $parameter): Response
    {
        $formFields = $request->query->all('form_fields');
        if (!is_array($formFields)) {
            $formFields = (array)[$formFields];
        }

        $data = [];
        $i = 0;
        foreach ($prospectRepository->findAll() as $prospect) {
            foreach ($formFields as $field) {
                $data[$i][] = $prospect->{'get' . ucfirst(str_replace('_', '', $field))}();
            }
            $i++;
        }

        // Utiliser le service pour générer le fichier Excel
        return $excelExporter->exportData('prospect_list', json_decode($parameter->findOneBy(['dtype' => 'prospect'])->getField(), true), $data, $formFields);
    }
}
