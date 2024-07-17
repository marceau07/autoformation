<?php

namespace App\Controller;

use App\Repository\TraineeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{_locale}/api/v1')]
class ApiController extends AbstractController
{
    #[Route('/', name: 'app_api', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json(
            [
                'success' => true,
                'message' => 'Welcome to the API',
            ],
            Response::HTTP_OK
        );
    }


    // #[Route('/trainees', name: 'app_api_get_trainees', methods: ['GET'])]
    // public function getTrainees(TraineeRepository $traineeRepository): JsonResponse
    // {
    //     $data = $traineeRepository->findAll();
    //     $results[] = [];
    //     $i = 0;
    //     foreach ($data as $d) {
    //         $results[$i]['id'] = $d->getId();
    //         $results[$i]['username'] = $d->getUsername();
    //         $results[$i]['lastName'] = $d->getLastName();
    //         $results[$i]['firstName'] = $d->getFirstName();
    //         $results[$i]['roles'] = $d->getRoles();
    //         $results[$i]['email'] = $d->getEmail();
    //         $results[$i]['activated'] = $d->isActivated();
    //         $results[$i]['tmpCode'] = $d->getTmpCode();
    //         $results[$i]['tmpCodeDate'] = $d->getTmpCodeDate();
    //         $i++;
    //     }

    //     return $this->json(
    //         [
    //             'success' => true,
    //             'trainees' => $results,
    //         ],
    //         status: 200
    //     );
    // }
}
