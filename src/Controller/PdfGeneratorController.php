<?php

namespace App\Controller;

use App\Repository\ProspectRepository;
use App\Repository\TraineeRepository;
use App\Repository\UserRepository;
use Dompdf\Adapter\PDFLib;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PdfGeneratorController extends AbstractController
{
    #[Route('/generate/{element}/{html?}', name: 'pdf_generate')]
    public function index(Request $request, ProspectRepository $prospectRepository, UserRepository $userRepository, TraineeRepository $traineeRepository, string $element)
    {
        // To generate base64 image for displaying in PDF
        // 'imageSrc'  => $this->imageToBase64($this->getParameter('kernel.project_dir') . '/assets/images/avatars/default.jpeg'),
        // $trainee = $traineeRepository->find($userRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()])->getId());

        // dump($trainee);
        $data = [
            'imageSrc'  => $this->imageToBase64($this->getParameter('kernel.project_dir') . '/assets/images/avatars/default.jpeg'),
            // 'name'         => $trainee->getLastName() . " " . $trainee->getFirstName(),
            'name'         => " ",
            'address'      => 'USA',
            'mobileNumber' => '000000000',
            'email'        => 'john.doe@email.com',
            'prospect'        => $prospectRepository->find(1)
        ];

        if ($request->attributes->has('element')) {
            switch ($request->attributes->get('element')) {
                case 'resume':
                    $html = $this->renderView('pdf_generator/resume.html.twig', $data);
                    break;
                case 'internship-agreement':
                    $html = $this->renderView('pdf_generator/internship_agreement.html.twig', $data);
                    break;
                case 'internship-certificate':
                    $html = $this->renderView('pdf_generator/internship_certificate.html.twig', $data);
                    break;
                case 'internship-evaluation':
                    $html = $this->renderView('pdf_generator/internship_evaluation.html.twig', $data);
                    break;
                default:
                    return $this->redirectToRoute('app_home');
            }
        } else {
            return $this->redirectToRoute('app_home');
        }

        // Return HTML or PDF
        if ($request->attributes->get('html') == true) {
            return new Response(
                $html,
                Response::HTTP_OK,
                ['content-type' => 'text/html']
            );
        } else {
            $dompdf = new Dompdf();

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            // $dompdf->setOptions(new Options(['isHtml5Parser' => true]));
            $dompdf->render();


            return new PDFLib(
                $dompdf->stream('resume', ["Attachment" => false]),
            );
        }
    }

    private function imageToBase64($path)
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }
}
