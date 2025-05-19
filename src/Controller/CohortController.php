<?php

namespace App\Controller;

use App\Repository\CohortRepository;
use App\Repository\ExportParameterRepository;
use App\Service\ExcelExporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_TRAINER')]
#[Route('/{_locale}/cohort')]
class CohortController extends AbstractController
{
    #[Route('/export/', name: 'app_cohort_export', methods: ['GET'], priority: 1)]
    public function export(Request $request, ExcelExporter $excelExporter, CohortRepository $cohortRepository, ExportParameterRepository $parameter): Response
    {
        $formFields = $request->query->all('form_fields');
        if (!is_array($formFields)) {
            $formFields = (array)[$formFields];
        }

        $data = [];
        $i = 0;
        foreach ($cohortRepository->findAll() as $cohort) {
            foreach ($formFields as $field) {
                $data[$i][] = $cohort->{'get' . ucfirst(str_replace('_', '', $field))}();
            }
            $i++;
        }

        // Utiliser le service pour générer le fichier Excel
        return $excelExporter->exportData('cohort_list', json_decode($parameter->findOneBy(['dtype' => 'cohort'])->getField(), true), $data, $formFields);
    }

    #[Route('/export-content', name: 'app_cohort_export_content', priority: 1)]
    public function exportContent(ExportParameterRepository $repository): Response
    {
        // On récupère tous les paramètres liés à un label donné ("cohort_x" par exemple)
        $fields = $repository->findOneBy(['dtype' => 'cohort'])->getField();

        return $this->render('admin/fields/export_modal_content.html.twig', [
            'fields' => $fields,
        ]);
    }
}
