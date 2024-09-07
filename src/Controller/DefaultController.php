<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Entity\Message;
use App\Entity\Notification;
use App\Entity\TraineeResource;
use App\Repository\CohortRepository;
use App\Repository\CourseResourceRepository;
use App\Repository\FeedbackCategoryRepository;
use App\Repository\NotificationRepository;
use App\Repository\TraineeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DefaultController extends AbstractController
{
    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/', name: 'app_default', methods: "GET")]
    public function index(): Response
    {
        return $this->redirectToRoute('app_home');
    }

    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/feedback', name: 'app_feedback', methods: "POST")]
    public function feedback(FeedbackCategoryRepository $feedbackCategoryRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $feedback = new Feedback();
        $feedback->setCategory($feedbackCategoryRepository->find($request->request->get('form_feedback_category')));
        $feedback->setAnnotation($request->request->get('form_feedback_annotation'));
        $feedback->setLink($request->request->get('form_feedback_link'));
        $feedback->setWeight($request->request->get('form_feedback_weight'));
        $feedback->setUser($this->getUser());
        $entityManager->persist($feedback);
        $entityManager->flush();

        return $this->json(
            [
                'success' => true,
                'message' => "Votre message a bien été envoyé !",
            ],
            status: Response::HTTP_OK
        );
    }

    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/{_locale}/chatbot', name: 'app_chatbot', methods: "POST")]
    public function chatbot(TranslatorInterface $translator, Request $request): Response
    {
        $userMessage = strtolower(trim($request->request->get('form_chatbot_message')));

        if (substr($userMessage, 0, 1) === "/") {
            $userMessage = substr($userMessage, 1);
            switch ($userMessage) {
                case "help":
                default:
                    $response = '<ul>
                                    <li><code>/' . $translator->trans('chatbot.keys.courses') . '&nbsp;{uuid|keywords}</code><p><small>' . $translator->trans('chatbot.commands.courses') . '</small></p></li>
                                    <li><code>/' . $translator->trans('chatbot.keys.modules') . '&nbsp;{uuid|keywords}</code><p><small>' . $translator->trans('chatbot.commands.modules') . '</small></p></li>
                                    <li><code>/' . $translator->trans('chatbot.keys.users') . '&nbsp;{uuid|username}</code><p><small>' . $translator->trans('chatbot.commands.users') . '</small></p></li>
                                </ul>';
                    break;
            }
        } else {
            $responses = [
                $translator->trans('chatbot.keys.hello', [], null, $request->getLocale()) => $translator->trans('chatbot.values.hello', [], null, $request->getLocale()),
                $translator->trans('chatbot.keys.hey', [], null, $request->getLocale()) => $translator->trans('chatbot.values.hey', [], null, $request->getLocale()),
                $translator->trans('chatbot.keys.help', [], null, $request->getLocale()) => $translator->trans('chatbot.values.help', [], null, $request->getLocale()),
                $translator->trans('chatbot.keys.courses', [], null, $request->getLocale()) => $translator->trans('chatbot.values.courses', [], null, $request->getLocale()),
                $translator->trans('chatbot.keys.goodbye', [], null, $request->getLocale()) => $translator->trans('chatbot.values.goodbye', [], null, $request->getLocale()),
            ];

            $response = $translator->trans('chatbot.didnt_understand', [], null, $request->getLocale());

            $closest = null;
            $shortest = -1;

            foreach ($responses as $keyword => $botResponse) {
                $lev = levenshtein($keyword, strtolower($userMessage));

                if ($lev == 0) {
                    $closest = $keyword;
                    $shortest = 0;
                    break;
                }

                if ($lev <= $shortest || $shortest < 0) {
                    $closest = $keyword;
                    $shortest = $lev;
                }
            }

            if ($closest !== null && $shortest < 5) { // You can set the threshold to define how "close" it needs to be
                $response = $responses[$closest];
            }
        }

        return $this->json(
            [
                'success' => true,
                'message' => $response,
            ],
            status: Response::HTTP_OK
        );
    }

    // TODO: Manage the good internship for the document
    #[IsGranted(new Expression('is_granted("ROLE_TRAINEE")'))]
    #[Route('/send_agreement', name: 'app_send_agreement', methods: "POST")]
    public function sendAgreement(Request $request, EntityManagerInterface $entityManager, TraineeRepository $traineeRepository): Response
    {
        if ($request->isXmlHttpRequest()) {
            $file = $request->files->get('file');

            if ($file) {
                if ($file->getSize() <= 52428800) {
                    if ($file->getMimeType() == 'application/pdf' || $file->getMimeType() == 'application/x-pdf') {
                        $trainee = $traineeRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
                        $nomFichier = "Convention_de_stage_" . strtoupper(str_replace(" ", "-", $trainee->getLastName())) . "_" . ucfirst(str_replace(" ", "-", $trainee->getFirstName())) . '.' . $file->guessExtension();
                        try {
                            $file->move($this->getParameter('internships_directory') . "/tmp", $nomFichier);
                            $this->addFlash('info', 'Convention de stage envoyée');
                            $documents = json_decode($trainee->getDocuments(), true);
                            $documents['internships'][0]['agreement'] = 2;
                            $trainee->setDocuments(json_encode($documents));
                            $entityManager->persist($trainee);

                            $notification = new Notification();
                            $notification->setOrigin($this->getUser()->getUserIdentifier());
                            $notification->setMessage("convention");
                            $notification->setLink("/../internships/tmp/" . $nomFichier);
                            $notification->setCategory("new_internship");
                            $notification->setDate(new \DateTimeImmutable());
                            $notification->setUser($trainee->getCohort()->getTrainer());
                            $entityManager->persist($notification);

                            $entityManager->flush();
                        } catch (FileException $e) {
                            $this->addFlash('danger', 'Erreur lors de l\'envoi de la convention: ' . $e->getMessage());
                        }
                        return $this->json(
                            [
                                'success' => true,
                                'message' => "Le fichier a été envoyé avec succès !",
                            ],
                            status: Response::HTTP_OK
                        );
                    }
                    return $this->json(
                        [
                            'success' => false,
                            'message' => "Le fichier n'est pas un fichier PDF...",
                        ],
                        status: Response::HTTP_BAD_REQUEST
                    );
                }
                return $this->json(
                    [
                        'success' => false,
                        'message' => "Le fichier ne doit pas dépasser 50Mo... (" . $file->getSize() . " octets)",
                    ],
                    status: Response::HTTP_BAD_REQUEST
                );
            }
            return $this->json(
                [
                    'success' => false,
                    'message' => "Le fichier ne semble pas avoir été téléchargé correctement...",
                ],
                status: Response::HTTP_BAD_REQUEST
            );
        }
        return $this->json(
            [
                'success' => false,
                'message' => "Veuillez réessayer plus tard...",
            ],
            status: Response::HTTP_NOT_FOUND
        );
    }
    #[IsGranted(new Expression('is_granted("ROLE_TRAINEE")'))]
    #[Route('/send_certificate', name: 'app_send_certificate', methods: "POST")]
    public function sendCertificate(Request $request, EntityManagerInterface $entityManager, TraineeRepository $traineeRepository): Response
    {
        if ($request->isXmlHttpRequest()) {
            $file = $request->files->get('file');

            if ($file) {
                if ($file->getSize() <= 52428800) {
                    if ($file->getMimeType() == 'application/pdf' || $file->getMimeType() == 'application/x-pdf') {
                        $trainee = $traineeRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
                        $nomFichier = "Attestation_de_stage_" . strtoupper(str_replace(" ", "-", $trainee->getLastName())) . "_" . ucfirst(str_replace(" ", "-", $trainee->getFirstName())) . '.' . $file->guessExtension();
                        try {
                            $file->move($this->getParameter('internships_directory') . "/tmp", $nomFichier);
                            $this->addFlash('info', 'Attestation de stage envoyée');
                            $documents = json_decode($trainee->getDocuments(), true);
                            $documents['internships'][0]['certificate'] = 2;
                            $trainee->setDocuments(json_encode($documents));
                            $entityManager->persist($trainee);

                            $notification = new Notification();
                            $notification->setOrigin($this->getUser()->getUserIdentifier());
                            $notification->setMessage("attestation");
                            $notification->setLink("/../internships/tmp/" . $nomFichier);
                            $notification->setCategory("new_internship");
                            $notification->setDate(new \DateTimeImmutable());
                            $notification->setUser($trainee->getCohort()->getTrainer());
                            $entityManager->persist($notification);

                            $entityManager->flush();
                        } catch (FileException $e) {
                            $this->addFlash('danger', 'Erreur lors de l\'envoi de l\'attestation de stage: ' . $e->getMessage());
                        }
                        return $this->json(
                            [
                                'success' => true,
                                'message' => "Le fichier a été envoyé avec succès !",
                            ],
                            status: Response::HTTP_OK
                        );
                    }
                    return $this->json(
                        [
                            'success' => false,
                            'message' => "Le fichier n'est pas un fichier PDF...",
                        ],
                        status: Response::HTTP_BAD_REQUEST
                    );
                }
                return $this->json(
                    [
                        'success' => false,
                        'message' => "Le fichier ne doit pas dépasser 50Mo... (" . $file->getSize() . " octets)",
                    ],
                    status: Response::HTTP_BAD_REQUEST
                );
            }
            return $this->json(
                [
                    'success' => false,
                    'message' => "Le fichier ne semble pas avoir été téléchargé correctement...",
                ],
                status: Response::HTTP_BAD_REQUEST
            );
        }
        return $this->json(
            [
                'success' => false,
                'message' => "Veuillez réessayer plus tard...",
            ],
            status: Response::HTTP_NOT_FOUND
        );
    }

    #[IsGranted(new Expression('is_granted("ROLE_TRAINEE")'))]
    #[Route('/send_evaluation', name: 'app_send_evaluation', methods: "POST")]
    public function sendEvaluation(Request $request, EntityManagerInterface $entityManager, TraineeRepository $traineeRepository): Response
    {
        if ($request->isXmlHttpRequest()) {
            $file = $request->files->get('file');

            if ($file) {
                if ($file->getSize() <= 52428800) {
                    if ($file->getMimeType() == 'application/pdf' || $file->getMimeType() == 'application/x-pdf') {
                        $trainee = $traineeRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
                        $nomFichier = "Evaluation_de_stage_" . strtoupper(str_replace(" ", "-", $trainee->getLastName())) . "_" . ucfirst(str_replace(" ", "-", $trainee->getFirstName())) . '.' . $file->guessExtension();
                        try {
                            $file->move($this->getParameter('internships_directory') . "/tmp", $nomFichier);
                            $this->addFlash('info', 'Évaluation de stage envoyée');
                            $documents = json_decode($trainee->getDocuments(), true);
                            $documents['internships'][0]['evaluation'] = 2;
                            $trainee->setDocuments(json_encode($documents));
                            $entityManager->persist($trainee);

                            $notification = new Notification();
                            $notification->setOrigin($this->getUser()->getUserIdentifier());
                            $notification->setMessage("evaluation");
                            $notification->setLink("/../internships/tmp/" . $nomFichier);
                            $notification->setCategory("new_internship");
                            $notification->setDate(new \DateTimeImmutable());
                            $notification->setUser($trainee->getCohort()->getTrainer());
                            $entityManager->persist($notification);

                            $entityManager->flush();
                        } catch (FileException $e) {
                            $this->addFlash('danger', 'Erreur lors de l\'envoi de l\'évaluation: ' . $e->getMessage());
                        }
                        return $this->json(
                            [
                                'success' => true,
                                'message' => "Le fichier a été envoyé avec succès !",
                            ],
                            status: Response::HTTP_OK
                        );
                    }
                    return $this->json(
                        [
                            'success' => false,
                            'message' => "Le fichier n'est pas un fichier PDF...",
                        ],
                        status: Response::HTTP_BAD_REQUEST
                    );
                }
                return $this->json(
                    [
                        'success' => false,
                        'message' => "Le fichier ne doit pas dépasser 50Mo... (" . $file->getSize() . " octets)",
                    ],
                    status: Response::HTTP_BAD_REQUEST
                );
            }
            return $this->json(
                [
                    'success' => false,
                    'message' => "Le fichier ne semble pas avoir été téléchargé correctement...",
                ],
                status: Response::HTTP_BAD_REQUEST
            );
        }
        return $this->json(
            [
                'success' => false,
                'message' => "Veuillez réessayer plus tard...",
            ],
            status: Response::HTTP_NOT_FOUND
        );
    }

    #[IsGranted(new Expression('is_granted("ROLE_TRAINER") or is_granted("ROLE_TRAINEE")'))]
    #[Route('/send_message', name: 'app_send_message', methods: "POST")]
    public function sendMessage(Request $request, UserRepository $userRepository, CohortRepository $cohortRepository, EntityManagerInterface $entityManager): Response
    {
        $notificationNewMessage = new Notification();
        $notificationNewMessage->setDate(new \DateTimeImmutable());
        $notificationNewMessage->setCategory("new_message");
        $notificationNewMessage->setOrigin($this->getUser()->getUserIdentifier());
        $notificationNewMessage->setMessage($this->getUser()->getUserIdentifier());

        $message = new Message();
        $message->setContent($request->request->get('form_message'));
        $message->setDate(new \DateTimeImmutable());
        if ($this->isGranted("ROLE_TRAINER")) {
            $message->setSendTrainer($this->getUser());

            $notificationNewMessage->setLink($this->generateUrl('app_mailbox_trainer', ['uuid' => $request->request->get('form_sender_uuid')]), true);
        } elseif ($this->isGranted("ROLE_TRAINEE")) {
            $message->setSendTrainee($this->getUser());

            $notificationNewMessage->setLink($this->generateUrl('app_mailbox_trainee', ['uuid' => $request->request->get('form_sender_uuid')]), true);
        }

        if (!empty($request->request->get('form_origin')) && $request->request->get('form_origin') == "cohort") {
            $cohort = $cohortRepository->findOneBy(['uuid' => $request->request->get('form_receiver_uuid')]);
            $message->setCohort($cohort);

            $cohortTrainees = $cohort->getTrainees();
            foreach ($cohortTrainees as $cohortTrainee) {
                $notificationNewMessage = new Notification();
                $notificationNewMessage->setDate(new \DateTimeImmutable());
                $notificationNewMessage->setCategory("new_message");
                $notificationNewMessage->setOrigin($this->getUser()->getUserIdentifier());
                $notificationNewMessage->setUser($cohortTrainee);
                $notificationNewMessage->setMessage($cohort->getName());
                $notificationNewMessage->setLink($this->generateUrl('app_mailbox_cohort', ['uuid' => $cohort->getUuid()]), true);
                $entityManager->persist($notificationNewMessage);
            }
        } elseif ((!empty($request->request->get('form_origin')) && $request->request->get('form_origin') == "trainee")) {
            $trainee = $userRepository->findOneBy(['uuid' => $request->request->get('form_receiver_uuid')]);
            $message->setTrainee($trainee);

            $notificationNewMessage->setUser($trainee);
        } elseif ((!empty($request->request->get('form_origin')) && $request->request->get('form_origin') == "trainer")) {
            $trainer = $userRepository->findOneBy(['uuid' => $request->request->get('form_receiver_uuid')]);
            $message->setTrainer($trainer);

            $notificationNewMessage->setUser($trainer);
        }
        $message->setContent($request->request->get('form_message'));

        $entityManager->persist($message);
        $entityManager->persist($notificationNewMessage);
        $entityManager->flush();

        // Get the referer URL from the request headers
        $referer = $request->headers->get('referer');

        // If the referer is not available, you can set a default route
        if ($referer) {
            return $this->redirect($referer);
        } else {
            return $this->redirectToRoute('app_mailbox');
        }
    }

    #[IsGranted(new Expression('is_granted("ROLE_TRAINEE")'))]
    #[Route('/send_tp', name: 'app_send_tp', methods: "POST")]
    public function sendTp(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager, CourseResourceRepository $courseResourceRepository, NotificationRepository $notificationRepository, UserRepository $userRepository): Response
    {
        if ($request->isXmlHttpRequest()) {
            $file = $request->files->get('file');

            if ($file) {
                if ($file->getSize() <= 52428800) {
                    if ($file->getMimeType() == 'application/pdf' || $file->getMimeType() == 'application/x-pdf' || $file->getMimeType() == 'application/x-rar-compressed' || $file->getMimeType() == 'application/x-tar' || $file->getMimeType() == 'application/zip' || $file->getMimeType() == 'application/x-7z-compressed') {
                        $nomFichier = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $nomFichier = $slugger->slug($nomFichier);
                        $nomFichier = $nomFichier . '-' . uniqid() . '.' . $file->guessExtension();
                        try {
                            $file->move($this->getParameter('homeworks_directory') . '/' . $this->getUser()->getUserIdentifier(), $nomFichier);
                            $this->addFlash('notice', 'Travail envoyé');
                            $traineeResource = new TraineeResource();
                            $traineeResource->setLabel($nomFichier);
                            $courseResource = $courseResourceRepository->find($request->request->get('tp_id'));
                            $traineeResource->setCourseResource($courseResource);
                            $traineeResource->setTrainee($this->getUser());
                            $entityManager->persist($traineeResource);
                            $entityManager->flush();

                            $currentUser = $userRepository->findOneBy(["username" => $this->getUser()->getUserIdentifier()]);
                            $notificationRepository->deleteANotification($courseResource->getCourse()->getModule()->getLabel(), null, "homework_to_do", $currentUser->getId());
                        } catch (FileException $e) {
                            $this->addFlash('danger', 'Erreur lors de l\'envoi du travail: ' . $e->getMessage());
                        }
                        return $this->json(
                            [
                                'success' => true,
                                'message' => "Le fichier a été envoyé avec succès !",
                            ],
                            status: Response::HTTP_OK
                        );
                    }
                    return $this->json(
                        [
                            'success' => false,
                            'message' => "Le fichier n'est pas un fichier PDF...",
                        ],
                        status: Response::HTTP_BAD_REQUEST
                    );
                }
                return $this->json(
                    [
                        'success' => false,
                        'message' => "Le fichier ne doit pas dépasser 50Mo... (" . $file->getSize() . " octets)",
                    ],
                    status: Response::HTTP_BAD_REQUEST
                );
            }
            return $this->json(
                [
                    'success' => false,
                    'message' => "Le fichier ne semble pas avoir été téléchargé correctement...",
                ],
                status: Response::HTTP_BAD_REQUEST
            );
        }
        return $this->json(
            [
                'success' => false,
                'message' => "Veuillez réessayer plus tard...",
            ],
            status: Response::HTTP_NOT_FOUND
        );
    }
}
