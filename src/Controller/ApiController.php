<?php

namespace App\Controller;

use App\Entity\QuizShare;
use App\Entity\User;
use App\Entity\UserQuiz;
use App\Repository\QuizRepository;
use App\Repository\QuizRowRepository;
use App\Repository\QuizShareRepository;
use App\Repository\UserQuizRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{_locale}/api/v1')]
class ApiController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(UserPasswordHasherInterface $passwordHasher, JWTTokenManagerInterface $jwtManager)
    {
        $this->passwordHasher = $passwordHasher;
        $this->jwtManager = $jwtManager;
    }

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

    #[Route('/login', name: 'app_api_login', methods: ['POST'])]
    public function login(Request $request, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['username']) && isset($data['password'])) {
            $username = $data['username'];
            $password = $data['password'];

            $user = $userRepository->findOneBy(['username' => $username]);
            if ($user instanceof User) {
                // Vérifie le mot de passe
                if ($this->passwordHasher->isPasswordValid($user, $password)) {
                    // Crée un token JWT pour l'utilisateur
                    $token = $this->jwtManager->create($user);
                    return $this->json(
                        [
                            'success' => true,
                            'username' => $user->getUsername(),
                            'uuid' => $user->getUuid(),
                            'token' => $token,
                        ],
                        Response::HTTP_OK
                    );
                }
            }
            return $this->json(
                [
                    'success' => false,
                    'message' => 'User or password not found',
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        return $this->json(
            [
                'success' => false,
                'message' => 'Username and password are required',
            ],
            Response::HTTP_BAD_REQUEST
        );
    }

    #[Route('/send/quiz', name: 'app_api_quiz_send', methods: ['POST'])]
    public function sendQuiz(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, QuizShareRepository $quizShareRepository, QuizRowRepository $quizRowRepository, QuizRepository $quizRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['userUuid']) && isset($data['questions'])) {
            $userUuid = $data['userUuid'];
            $quizUuid = $data['quizUuid'];
            $answers = $data['questions'];

            $quizRow = $quizShareRepository->findOneBy(['uuid' => $quizUuid])->getQuiz()->getQuizRows();
            if (!empty($quizRow) && !empty($answers)) {
                foreach ($quizRow as $key => $row) {
                    foreach ($answers as $key2 => $value) {
                        if ($key == $key2) {
                            $userQuiz = new UserQuiz();
                            $user = $userRepository->findOneBy(['uuid' => $userUuid]);
                            $userQuiz->setAnswer($value);
                            $userQuiz->setUser($user);
                            $userQuiz->setQuizRow($row);
                            $entityManager->persist($userQuiz);
                        }
                    }
                }
                $entityManager->flush();
                return $this->json(
                    [
                        'success' => true,
                        'message' => 'Quiz sent',
                    ],
                    Response::HTTP_OK
                );
            }
            return $this->json(
                [
                    'success' => false,
                    'message' => 'Quiz not found',
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json(
            [
                'success' => false,
                'message' => 'UUID and answers are required',
            ],
            Response::HTTP_BAD_REQUEST
        );
    }

    #[Route('/quiz', name: 'app_api_quiz_fetch', methods: ['GET'])]
    public function quizzes(QuizShareRepository $quizShareRepository, Packages $packages): JsonResponse
    {
        $data = $quizShareRepository->findAll();
        $quizzes[] = [];
        $quiz_visible = [];
        $i = 0;
        foreach ($data as $d) {
            if ($d->isAvailable()) {
                if (!in_array($d->getQuiz()->getId(), $quiz_visible)) {
                    $quiz_visible[] = $d->getQuiz()->getId();
                    $quizzes[$i]['id'] = $d->getQuiz()->getId();
                    $quizzes[$i]['title'] = $d->getQuiz()->getTitle();
                    $quizzes[$i]['uuid'] = $d->getUuid();
                    $quizzes[$i]['module']['label'] = $d->getQuiz()->getModule()->getLabel();
                    $quizzes[$i]['module']['illustration'] = $packages->getUrl('images/' . $d->getQuiz()->getModule()->getIllustration());
                    $quizzes[$i]['theme']['name'] = $d->getQuiz()->getTheme()->getName();
                    $quizzes[$i]['theme']['color'] = $d->getQuiz()->getTheme()->getColor();
                    $quizzes[$i]['theme']['illustration'] = $packages->getUrl('images/quizzes/' . $d->getQuiz()->getTheme()->getIllustration());
                    $i++;
                }
            }
        }

        return $this->json(
            $quizzes,
            Response::HTTP_OK
        );
    }

    #[Route('/quiz/{uuid}', name: 'app_api_quiz', methods: ['GET'])]
    public function quiz(QuizShareRepository $quizShareRepository, string $uuid)
    {
        $data = $quizShareRepository->findOneBy(['uuid' => $uuid]);
        if ($data instanceof QuizShare) {
            $d = $data->getQuiz();
            $quiz = [
                'id' => $d->getId(),
                'title' => $d->getModule()->getLabel(),
                'uuid' => $data->getUuid(),
                'module' => [
                    'label' => $d->getModule()->getLabel(),
                    'illustration' => $d->getModule()->getIllustration(),
                ],
                'theme' => [
                    'name' => $d->getTheme()->getName(),
                    'color' => $d->getTheme()->getColor(),
                    'illustration' => $d->getTheme()->getIllustration(),
                ],
            ];
            return $this->json(
                $quiz,
                Response::HTTP_OK
            );
        }
        return $this->json(
            [
                'success' => false,
                'message' => 'Une erreur est survenue',
            ],
            Response::HTTP_NOT_FOUND
        );
    }

    #[Route('/quiz/{uuid}/rows', name: 'app_api_quiz_rows', methods: ['GET'])]
    public function quizRows(QuizShareRepository $quizShareRepository, string $uuid, Packages $packages): JsonResponse
    {
        $data = $quizShareRepository->findOneBy(['uuid' => $uuid]);
        $quizzes[] = [];
        if ($data instanceof QuizShare && $data->isAvailable()) {
            $i = 0;
            $d = $data->getQuiz();
            foreach ($d->getQuizRows() as $qr) {
                $quizzes[$i] = [
                    'uuid' => $qr->getUuid(),
                    'question' => $qr->getQuestion(),
                    'options' => [$qr->getOption1() ?? "", $qr->getOption2() ?? "", $qr->getOption3() ?? "", $qr->getOption4() ?? ""],
                    'answer' => $qr->getAnswer() ?? "",
                    'type' => $qr->getQuizType(),
                ];
                $quizzes[$i]['theme']['name'] = $d->getTheme()->getName();
                $quizzes[$i]['theme']['color'] = $d->getTheme()->getColor();
                $quizzes[$i]['theme']['illustration'] = $packages->getUrl('images/quizzes/' . $d->getTheme()->getIllustration());
                $i++;
            }
            return $this->json(
                $quizzes,
                Response::HTTP_OK
            );
        } elseif (!$data->isAvailable()) {
            if ($data->getStartDate() > new DateTimeImmutable()) {
                return $this->json(
                    [
                        [
                            'success' => false,
                            'message' => 'Le quiz n\'est pas encore disponible',
                        ]
                    ],
                    Response::HTTP_NOT_FOUND
                );
            } elseif ($data->getFinishDate() < new DateTimeImmutable()) {
                return $this->json(
                    [
                        [
                            'success' => false,
                            'message' => 'Ce quiz n\'est plus disponible',
                        ]
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }
            return $this->json(
                [
                    [
                        'success' => false,
                        'message' => 'Quiz not available',
                    ]
                ],
                Response::HTTP_NOT_FOUND
            );
        } else {
            return $this->json(
                [
                    'success' => false,
                    'message' => 'Quiz not found',
                ],
                Response::HTTP_NOT_FOUND
            );
        }
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
    //         $results[$i]['activated'] = $d->getActivated();
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
