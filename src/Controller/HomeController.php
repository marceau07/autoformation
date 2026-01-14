<?php

namespace App\Controller;

use App\Repository\CalendarRepository;
use App\Repository\CourseRepository;
use App\Repository\FaqRepository;
use App\Repository\CohortRepository;
use App\Repository\CoordinatorRepository;
use App\Repository\CourseModuleRepository;
use App\Repository\CourseTraineeRepository;
use App\Repository\MessageRepository;
use App\Repository\NotificationRepository;
use App\Repository\QuizShareRepository;
use App\Repository\ResponsibleRepository;
use App\Repository\SurveyRepository;
use App\Repository\SurveyTraineeRepository;
use App\Repository\TraineeCourseFavoriteRepository;
use App\Repository\TraineeRepository;
use App\Repository\TraineeResourceRepository;
use App\Repository\TrainerRepository;
use App\Repository\UserRepository;
use App\Service\SectorResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/{_locale}')]
class HomeController extends AbstractController
{
    // #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/home', name: 'app_home', methods: "GET")]
    public function home(TraineeRepository $traineeRepository, CourseRepository $courseRepository, CalendarRepository $calendarRepository, CourseTraineeRepository $courseTraineeRepository, SurveyTraineeRepository $surveyTraineeRepository): Response
    {
        $currentCalendar = [];
        // if ($this->isGranted('ROLE_TRAINER') === true) {
        //     $currentCalendar = $calendarRepository->getCurrentCalendar($this->getUser()->getUserIdentifier());
        // } elseif ($this->isGranted('ROLE_COORDINATOR') === true) {
        //     array_push($currentCalendar, $calendarRepository->getCurrentCalendar($traineeRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()])->getCohort()->getTrainer()->getId()));
        // } elseif ($this->isGranted('ROLE_RESPONSIBLE') === true) {
        //     $currentCalendar = $calendarRepository->getCurrentCalendar($traineeRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()])->getCohort()->getTrainer()->getCoordinator()->getId());
        // }

        if ($this->isGranted('ROLE_USER') === true) {
            return $this->render('home/index.html.twig', [
                'satisfactionSurvey' => $traineeRepository->getCohortsInformations($this->getUser()->getUserIdentifier()),
                'latestCourses' => $courseRepository->getLatestCoursesByTrainee($this->getUser()->getUserIdentifier()),
                'popularCourses' => ($this->isGranted('ROLE_TRAINER') ? $courseRepository->getPopularCoursesSector($this->getUser()->getUserIdentifier()) : $courseRepository->getPopularCoursesCohort($this->getUser()->getUserIdentifier())),
                'traineesOpinions' => $surveyTraineeRepository->getGlobalSurveys(),
                'currentCalendar' => $currentCalendar,
            ]);
        } else {
            return $this->redirectToRoute('app_presentation');
        }
    }

    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/modules', name: 'app_modules', methods: "GET")]
    public function modules(CourseRepository $courseRepository, ResponsibleRepository $responsibleRepository, CoordinatorRepository $coordinatorRepository, TrainerRepository $trainerRepository, SectorResolver $sectorResolver): Response
    {
        $user = null;
        if($this->isGranted('ROLE_RESPONSIBLE')) {
            $user = $responsibleRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        } else if($this->isGranted('ROLE_COORDINATOR')) {
            $user = $coordinatorRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        } else if($this->isGranted('ROLE_TRAINER')) {
            $user = $trainerRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        }

        $defaultModule = $courseRepository->findOneBy(['module' => 1]);
        $listModulesBasics = ($this->isGranted('ROLE_USER') && !empty($defaultModule) ? [$defaultModule] : []);
        $listModules = ($user !== null ? $courseRepository->getCoursesModulesBySector($sectorResolver->resolve($user)) : $courseRepository->getCoursesModulesByCohort($this->getUser()->getUserIdentifier()));
        
        return $this->render('course/module.html.twig', [
            'listModules' => array_merge($listModulesBasics, $listModules)
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/course/read/{course}/{search}', name: 'app_course', methods: "GET", requirements: ['search' => '.+'])]
    public function course(CourseRepository $courseRepository, ResponsibleRepository $responsibleRepository, CoordinatorRepository $coordinatorRepository, TrainerRepository $trainerRepository, CourseModuleRepository $courseModuleRepository, QuizShareRepository $quizShareRepository, string $course, ?string $search = null): Response
    {
        $user = null;
        if($this->isGranted('ROLE_RESPONSIBLE')) {
            $user = $responsibleRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        } else if($this->isGranted('ROLE_COORDINATOR')) {
            $user = $coordinatorRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        } else if($this->isGranted('ROLE_TRAINER')) {
            $user = $trainerRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        }
        $listCourses = ($user !== null ? $courseRepository->getCoursesInformationsBySector($course, $search) : $courseRepository->getCoursesInformationsByCohort($this->getUser()->getUserIdentifier(), $course, $search));

        $listQuizzes = [];
        $quiz_visible = [];
        $quizzes = $courseModuleRepository->findOneBy(['uuid' => $course])->getQuizzes();
        $shared = $quizShareRepository->findAll();
        foreach ($quizzes as $quiz) {
            foreach ($shared as $value) {
                if ($value->getQuiz()->getId() === $quiz->getId()) {
                    if (!in_array($value->getQuiz()->getId(), $quiz_visible)) {
                        $quiz_visible[] = $value->getQuiz()->getId();
                        $listQuizzes[] = $value;
                    }
                }
            }
        }

        return $this->render('course/course.html.twig', [
            'module' =>  $listCourses[0]->getModule()->getLabel() ?? $quizzes[0]->getModule()->getLabel(),
            'listCourses' => $listCourses,
            'listQuizzes' => $listQuizzes
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/quiz/launch/{uuid}', name: 'app_quiz', methods: "GET")]
    public function quiz(QuizShareRepository $quizShareRepository, string $uuid): Response
    {
        $quiz = null;
        $theQuiz = $quizShareRepository->findOneBy(['uuid' => $uuid]);
        if ($theQuiz->isAvailable()) {
            $quiz = $theQuiz;
        }

        return $this->render('quiz/launch.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/embed/{slide}', name: 'app_embed', methods: "GET")]
    public function embed(CourseRepository $courseRepository, TraineeCourseFavoriteRepository $traineeCourseFavoriteRepository, TraineeRepository $traineeRepository, UserRepository $userRepository, TraineeResourceRepository $traineeResourceRepository, NotificationRepository $notificationRepository, string $slide): Response
    {
        $tps = array();
        if ($this->isGranted('ROLE_TRAINEE') === true) {
            $currentUser = $userRepository->findOneBy(["username" => $this->getUser()->getUserIdentifier()]);
            $traineeRepository->updateCourseVisitors($slide);
            $traineeRepository->updateCourseFollowed($slide, $this->getUser()->getUserIdentifier());
            $tps = $traineeResourceRepository->findByTrainee($this->getUser()->getUserIdentifier());
            $notificationRepository->deleteANotification("new_course", $currentUser->getId(), "[" . $courseRepository->getCourseInformations($slide)->getModule()->getLabel() . "]", null);
        }

        return $this->render('course/embed.html.twig', [
            'course' => $courseRepository->getCourseInformations($slide),
            'courseInFavorites' => ($this->isGranted('ROLE_TRAINEE') ? $traineeCourseFavoriteRepository->findOneBy(['course' => $courseRepository->findOneBy(['link' => $slide]), 'trainee' => $traineeRepository->find($currentUser->getId())]) : false),
            'traineeResources' => $tps,
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/dl/{trainer}/{filename}', name: 'app_download', methods: "GET")]
    public function download(string $trainer, string $filename, TranslatorInterface $translator): Response
    {
        $path = $_SERVER['DOCUMENT_ROOT'] . "homeworks/trainers/" . $trainer . "/" . $filename;

        if (!file_exists($path)) {
            $this->addFlash('error', $translator->trans('global.file_not_found'));
            return $this->redirectToRoute('app_home');
        }
        $response = new BinaryFileResponse($path);
        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Content-Disposition', "attachment; filename=$filename");
        $response->sendHeaders();
        return $response;
    }

    #[IsGranted(new Expression('is_granted("ROLE_RESPONSIBLE") or is_granted("ROLE_COORDINATOR") or is_granted("ROLE_TRAINER") or is_granted("ROLE_TRAINEE")'))]
    #[Route('/mailbox/', name: 'app_mailbox', methods: "GET")]
    public function mailbox(CohortRepository $cohortRepository, TraineeRepository $traineeRepository): Response
    {
        return $this->render('mailbox/index.html.twig', [
            'cohorts' => (($this->isGranted('ROLE_RESPONSIBLE') || $this->isGranted('ROLE_COORDINATOR') || $this->isGranted('ROLE_TRAINER')) ? $cohortRepository->findAll() : [$traineeRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()])->getCohort()]),
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_RESPONSIBLE") or is_granted("ROLE_COORDINATOR") or is_granted("ROLE_TRAINER") or is_granted("ROLE_TRAINEE")'))]
    #[Route('/mailbox/cohort/{uuid}', name: 'app_mailbox_cohort', methods: "GET")]
    public function mailboxCohort(CohortRepository $cohortRepository, MessageRepository $messageRepository, TraineeRepository $traineeRepository, UserRepository $userRepository, NotificationRepository $notificationRepository, ?string $uuid = null): Response
    {
        $cohort = $cohortRepository->findOneBy(["uuid" => $uuid]);
        $messages = $messageRepository->getMessagesBetweenTraineesAndCohort($cohort->getUuid());

        $currentUser = $userRepository->findOneBy(["username" => $this->getUser()->getUserIdentifier()]);
        foreach ($messages as $message) {
            if ($message->getCohort() !== null && $message->getPeople() === null && $message->getSendPeople() === null) {
                $notificationRepository->deleteANotification("new_message", $currentUser->getId(), null, $cohort->getUuid());
            }
        }

        return $this->render('mailbox/index.html.twig', [
            'cohorts' => (($this->isGranted('ROLE_RESPONSIBLE') || $this->isGranted('ROLE_COORDINATOR') || $this->isGranted('ROLE_TRAINER')) ? $cohortRepository->findAll() : [$traineeRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()])->getCohort()]),
            'cohort' => $cohort,
            'messages' => $messages,
            'uuid' => $uuid,
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_RESPONSIBLE") or is_granted("ROLE_COORDINATOR") or is_granted("ROLE_TRAINER") or is_granted("ROLE_TRAINEE")'))]
    #[Route('/mailbox/{people}/{uuid}', name: 'app_mailbox_people', methods: "GET")]
    public function mailboxPeople(CohortRepository $cohortRepository, MessageRepository $messageRepository, TraineeRepository $traineeRepository, UserRepository $userRepository, NotificationRepository $notificationRepository, string $people, string $uuid): Response
    {
        $user = $userRepository->findOneBy(["username" => $this->getUser()->getUserIdentifier()]);
        if ($uuid == $user->getUuid()) {
            $this->addFlash('error', 'Vous ne pouvez pas vous envoyer de message à vous même.');
            return $this->redirectToRoute('app_mailbox');
        }

        $contact = $userRepository->findOneBy(["uuid" => $uuid]);
        $currentUser = $userRepository->findOneBy(["username" => $this->getUser()->getUserIdentifier()]);
        $messages = $messageRepository->getMessagesBetweenPeople($currentUser->getUuid(), $contact->getUuid());

        foreach ($messages as $message) {
            if ($message->getPeople() !== null && $message->getPeople()->getId() == $currentUser->getId()) {
                $messageRepository->makeMessageReaded($message->getId());
                $notificationRepository->deleteANotification("new_message", $currentUser->getId(), $contact->getUsername(), null);
            }
        }

        return $this->render('mailbox/index.html.twig', [
            'cohorts' => (($this->isGranted('ROLE_RESPONSIBLE') || $this->isGranted('ROLE_COORDINATOR') || $this->isGranted('ROLE_TRAINER')) ? $cohortRepository->findAll() : [$traineeRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()])->getCohort()]),
            'contact' => $contact,
            'messages' => $messages,
            'uuid' => $uuid,
            'trainer' => true
        ]);
    }

    #[Route('/q-and-a', name: 'app_q_and_a', methods: "GET")]
    public function faq(FaqRepository $faqRepository, TraineeRepository $traineeRepository, TrainerRepository $trainerRepository): Response
    {
        $sectorId = null;
        if ($this->isGranted('ROLE_TRAINEE') === true) {
            $sectorId = $traineeRepository->findOneBy(["username" => $this->getUser()->getUserIdentifier()])->getCohort()->getTrainer()->getCoordinator()->getResponsible()->getSector()->getId();
        } elseif ($this->isGranted('ROLE_TRAINER') === true) {
            $sectorId = $trainerRepository->findOneBy(["username" => $this->getUser()->getUserIdentifier()])->getCoordinator()->getResponsible()->getSector()->getId();
        }

        return $this->render('faq/faq.html.twig', [
            'listThemes' => $faqRepository->getThemes($sectorId),
            'listFaqs' => $faqRepository->getFaqs($sectorId)
        ]);
    }

    #[Route('/legal-notices', name: 'app_legal_notices', methods: "GET")]
    public function legalNotices(): Response
    {
        return $this->render('home/legal_notices.html.twig', [
            'serverName' => $_SERVER["SERVER_NAME"]
        ]);
    }

    #[Route('/privacy-policy', name: 'app_privacy_policy', methods: "GET")]
    public function privacyPolicy(): Response
    {
        return $this->render('home/privacy_policy.html.twig', [
            'serverName' => $_SERVER["SERVER_NAME"]
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_TRAINEE")'))]
    #[Route('/survey', name: 'app_survey', methods: ["GET", "POST"])]
    public function survey(SurveyRepository $surveyRepository, EntityManagerInterface $entityManager): Response
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // TODO: Save survey
            // foreach($_POST as $key => $value) {
            //     if (strpos($key, 'questionnaire_id_') !== false) {
            //         $surveyTrainee = new SurveyTrainee();
            //     }
            //     $surveyTrainee->setTrainee($this->getUser());
            //     $surveyTrainee->setSurvey($surveyRepository->findOneBy(["id" => explode('_', $key)[2]]));
            //     $surveyTrainee->setRate($value);
            //     $surveyTrainee->setAnswer($value);
            //     $entityManager->persist($surveyTrainee);
            // }
            // $entityManager->flush();

            $this->addFlash('notice', 'Merci pour votre participation !');
            return $this->redirectToRoute('app_home');
        }
        return $this->render('survey/index.html.twig', [
            'questions' => $surveyRepository->findAll()
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_RESPONSIBLE") or is_granted("ROLE_COORDINATOR") or is_granted("ROLE_TRAINER") or is_granted("ROLE_TRAINEE")'))]
    #[Route('/search', name: 'app_search', methods: ["GET", "POST"])]
    public function search(Request $request, SerializerInterface $serializer, CourseRepository $courseRepository, TraineeRepository $traineeRepository, TrainerRepository $trainerRepository, CoordinatorRepository $coordinatorRepository, ResponsibleRepository $responsibleRepository): Response
    {
        if ($request->isXmlHttpRequest()) {
            $search = $request->request->get('q');

            // $courses = $courseRepository->searchCourses($search);
            $courses = (($this->isGranted('ROLE_TRAINER') || $this->isGranted('ROLE_COORDINATOR') || $this->isGranted('ROLE_RESPONSIBLE')) ? $courseRepository->getCoursesInformationsBySector(null, $search) : $courseRepository->getCoursesInformationsByCohort($this->getUser()->getUserIdentifier(), null, $search));

            $trainees = $traineeRepository->searchTrainees($search);
            $trainers = $trainerRepository->searchTrainers($search);
            $coordinators = $coordinatorRepository->searchCoordinators($search);
            $responsibles = $responsibleRepository->searchResponsibles($search);
            return $this->json(
                [
                    'success' => true,
                    // 'courses' => json_decode($serializer->serialize($courses, 'json', ['groups' => ['course_search']]), true), // TODO: Fix this to fetch only courses in sector/allowed courses
                    'courses' => json_decode($serializer->serialize($courses, 'json', ['groups' => ['course_search']]), true),
                    'trainees' => json_decode($serializer->serialize($trainees, 'json', ['groups' => ['trainee_search']]), true),
                    'trainers' => json_decode($serializer->serialize($trainers, 'json', ['groups' => ['trainer_search']]), true),
                    'coordinators' => json_decode($serializer->serialize($coordinators, 'json', ['groups' => ['coordinator_search']]), true),
                    'responsibles' => json_decode($serializer->serialize($responsibles, 'json', ['groups' => ['responsible_search']]), true),
                ],
                status: Response::HTTP_OK
            );
        }
        return $this->json(
            [
                'success' => false,
                'message' => "Veuillez passer par le module de recherche...",
            ],
            status: Response::HTTP_BAD_REQUEST
        );
    }
}
