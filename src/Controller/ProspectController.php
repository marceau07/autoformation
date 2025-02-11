<?php

namespace App\Controller;

use App\Entity\Prospect;
use App\Form\ProspectType;
use App\Repository\ExportParameterRepository;
use App\Repository\ProspectRepository;
use App\Service\ExcelExporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_TRAINER')]
#[Route('/{_locale}/prospect')]
class ProspectController extends AbstractController
{
    #[Route('/', name: 'app_prospect_index', methods: ['GET'])]
    public function index(ProspectRepository $prospectRepository, ExportParameterRepository $parameter): Response
    {
        return $this->render('prospect/index.html.twig', [
            'prospects' => $prospectRepository->findAll(),
            'exportParameters' => json_decode($parameter->findOneBy(['dtype' => 'prospect'])->getField(), true),
        ]);
    }

    #[Route('/new', name: 'app_prospect_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $prospect = new Prospect();
        $form = $this->createForm(ProspectType::class, $prospect);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($prospect);
            $entityManager->flush();

            return $this->redirectToRoute('app_prospect_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('prospect/new.html.twig', [
            'prospect' => $prospect,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_prospect_show', methods: ['GET'])]
    public function show(Prospect $prospect): Response
    {
        return $this->render('prospect/show.html.twig', [
            'prospect' => $prospect,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_prospect_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Prospect $prospect, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProspectType::class, $prospect);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_prospect_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('prospect/edit.html.twig', [
            'prospect' => $prospect,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_prospect_delete', methods: ['POST'])]
    public function delete(Request $request, Prospect $prospect, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $prospect->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($prospect);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_prospect_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/export/', name: 'app_prospect_export', methods: ['GET'], priority: 1)]
    public function export(Request $request, ExcelExporter $excelExporter, ProspectRepository $prospectRepository, ExportParameterRepository $parameter): Response
    {
        $formFields = $request->query->all('form_fields');
        if(!is_array($formFields)) {
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
