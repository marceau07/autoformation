<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Entity\TraineeResource;
use App\Repository\CourseModuleRepository;
use App\Repository\CourseRepository;
use App\Repository\FaqRepository;
use App\Repository\CohortRepository;
use App\Repository\CourseCohortRepository;
use App\Repository\CourseTraineeRepository;
use App\Repository\FeedbackCategoryRepository;
use App\Repository\FeedbackRepository;
use App\Repository\MessageRepository;
use App\Repository\SurveyRepository;
use App\Repository\SurveyTraineeRepository;
use App\Repository\TraineeRepository;
use App\Repository\TraineeResourceRepository;
use App\Repository\TrainerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/{_locale}')]
class HomeController extends AbstractController
{
    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/', name: 'app_home', methods: "GET")]
    public function home(TraineeRepository $traineeRepository, CourseRepository $courseRepository, CourseTraineeRepository $courseTraineeRepository, SurveyTraineeRepository $surveyTraineeRepository): Response
    {
        if ($this->isGranted('ROLE_USER') === true) {
            return $this->render('home/index.html.twig', [
                'satisfactionSurvey' => $traineeRepository->getCohortsInformations($this->getUser()->getUserIdentifier()),
                'latestCourses' => $courseRepository->getLatestCoursesByTrainee($this->getUser()->getUserIdentifier()),
                'popularCourses' => ($this->isGranted('ROLE_TRAINER') ? $courseRepository->getPopularCoursesSector($this->getUser()->getUserIdentifier()) : $courseRepository->getPopularCoursesCohort($this->getUser()->getUserIdentifier())),
                'traineesOpinions' => $surveyTraineeRepository->getGlobalSurveys($this->getUser()->getUserIdentifier()),
            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }

    // // TODO: vérifier la faisabi
    // #[Route('/updateSessionFilter', name: '_update_session_filter', methods: "POST", condition: "request.isXmlHttpRequest()")]
    // public function updateSessionFilter(Request $request): Response
    // {
    //     return new JsonResponse(['test' => 'OK'], 200);
    // }

    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/modules', name: 'app_modules', methods: "GET")]
    public function modules(CourseRepository $courseRepository): Response
    {
        $listModules = ($this->isGranted('ROLE_TRAINER') ? $courseRepository->getCoursesModulesBySector($this->getUser()->getUserIdentifier()) : $courseRepository->getCoursesModulesByCohort($this->getUser()->getUserIdentifier()));

        return $this->render('course/module.html.twig', [
            'listModules' => $listModules
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/course/read/{course}/{search}', name: 'app_course', methods: "GET")]
    public function course(CourseRepository $courseRepository, string $course, string $search = null): Response
    {
        return $this->render('course/course.html.twig', [
            'listCourses' => $courseRepository->getCoursesInformations($course, $search)
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/embed/{slide}', name: 'app_embed', methods: "GET")]
    public function embed(CourseRepository $courseRepository, TraineeRepository $traineeRepository, TraineeResourceRepository $traineeResourceRepository, string $slide): Response
    {
        $tps = array();
        if ($this->isGranted('ROLE_TRAINEE') === true) {
            $traineeRepository->updateCourseVisitors($slide);
            $traineeRepository->updateCourseFollowed($slide, $this->getUser()->getUserIdentifier());
            $tps = $traineeResourceRepository->findByTrainee($this->getUser()->getUserIdentifier());
        }

        return $this->render('course/embed.html.twig', [
            'course' => $courseRepository->getCourseInformations($slide),
            'traineeResources' => $tps
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/dl/{filename}', name: 'app_download', methods: "GET")]
    public function download(string $filename, TrainerRepository $trainerRepository): Response
    {
        $path = $_SERVER['DOCUMENT_ROOT'] . "homeworks/trainers/" . $filename;

        if (!file_exists($path)) {
            throw $this->createNotFoundException();
        }
        $response = new BinaryFileResponse($path);
        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Content-Disposition', "attachment; filename=$filename");
        $response->sendHeaders();
        return $response;
    }

    #[IsGranted(new Expression('is_granted("ROLE_TRAINER")'))]
    #[Route('/admin', name: 'app_admin_dashboard', methods: "GET")]
    public function adminDashboard(): Response
    {


        return $this->render('home/admin.html.twig', []);
    }

    #[IsGranted(new Expression('is_granted("ROLE_TRAINER") or is_granted("ROLE_TRAINEE")'))]
    #[Route('/mailbox/', name: 'app_mailbox', methods: "GET")]
    public function mailbox(CohortRepository $cohortRepository, TrainerRepository $trainerRepository, TraineeRepository $traineeRepository): Response
    {
        return $this->render('mailbox/index.html.twig', [
            'cohorts' => ($this->isGranted('ROLE_TRAINER') ? $cohortRepository->findAll() : [$traineeRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()])->getCohort()]),
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_TRAINER") or is_granted("ROLE_TRAINEE")'))]
    #[Route('/mailbox/cohort/{uuid}', name: 'app_mailbox_cohort', methods: "GET")]
    public function mailboxCohort(CohortRepository $cohortRepository, MessageRepository $messageRepository, TraineeRepository $traineeRepository, string $uuid = null): Response
    {
        $cohort = $cohortRepository->findOneBy(["uuid" => $uuid]);
        $messages = $messageRepository->getMessagesBetweenTraineesAndCohort($cohort->getUuid());

        return $this->render('mailbox/index.html.twig', [
            'cohorts' => ($this->isGranted('ROLE_TRAINER') ? $cohortRepository->findAll() : [$traineeRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()])->getCohort()]),
            'cohort' => $cohort,
            'messages' => $messages,
            'uuid' => $uuid,
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_TRAINER") or is_granted("ROLE_TRAINEE")'))]
    #[Route('/mailbox/trainee/{uuid}', name: 'app_mailbox_trainee', methods: "GET")]
    public function mailboxTrainee(CohortRepository $cohortRepository, MessageRepository $messageRepository, TraineeRepository $traineeRepository, UserRepository $userRepository, string $uuid = null): Response
    {
        $user = $userRepository->findOneBy(["username" => $this->getUser()->getUserIdentifier()]);
        if ($uuid == $user->getUuid()) {
            $this->addFlash('error', 'Vous ne pouvez pas vous envoyer de message à vous même.');
            return $this->redirectToRoute('app_mailbox');
        }

        $contact = $userRepository->findOneBy(["uuid" => $uuid]);
        $currentUser = $userRepository->findOneBy(["username" => $this->getUser()->getUserIdentifier()]);
        if ($this->isGranted('ROLE_TRAINER')) {
            $messages = $messageRepository->getMessages($currentUser->getUuid(), $contact->getUuid());
        } else {
            $messages = $messageRepository->getMessagesBetweenTrainees($currentUser->getUuid(), $contact->getUuid());
        }

        return $this->render('mailbox/index.html.twig', [
            'cohorts' => ($this->isGranted('ROLE_TRAINER') ? $cohortRepository->findAll() : [$traineeRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()])->getCohort()]),
            'contact' => $contact,
            'messages' => $messages,
            'uuid' => $uuid,
            'trainee' => true
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_TRAINER") or is_granted("ROLE_TRAINEE")'))]
    #[Route('/mailbox/trainer/{uuid}', name: 'app_mailbox_trainer', methods: "GET")]
    public function mailboxTrainer(CohortRepository $cohortRepository, MessageRepository $messageRepository, TraineeRepository $traineeRepository, UserRepository $userRepository, string $uuid = null): Response
    {
        $user = $userRepository->findOneBy(["username" => $this->getUser()->getUserIdentifier()]);
        if ($uuid == $user->getUuid()) {
            $this->addFlash('error', 'Vous ne pouvez pas vous envoyer de message à vous même.');
            return $this->redirectToRoute('app_mailbox');
        }

        $contact = $userRepository->findOneBy(["uuid" => $uuid]);
        $currentUser = $userRepository->findOneBy(["username" => $this->getUser()->getUserIdentifier()]);
        if ($this->isGranted('ROLE_TRAINEE')) {
            $messages = $messageRepository->getMessages($currentUser->getUuid(), $contact->getUuid());
        } else {
            $messages = $messageRepository->getMessagesBetweenTrainers($currentUser->getUuid(), $contact->getUuid());
        }

        foreach ($messages as $message) {
            if ($message->getTrainer() !== null && $message->getTrainer()->getId() == $currentUser->getId() || $message->getTrainee() !== null && $message->getTrainee()->getId() == $currentUser->getId()) {
                $messageRepository->makeMessageReaded($message->getId());
            }
        }

        return $this->render('mailbox/index.html.twig', [
            'cohorts' => ($this->isGranted('ROLE_TRAINER') ? $cohortRepository->findAll() : [$traineeRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()])->getCohort()]),
            'contact' => $contact,
            'messages' => $messages,
            'uuid' => $uuid,
            'trainer' => true
        ]);
    }

    #[Route('/q&a', name: 'app_q_and_a', methods: "GET")]
    public function faq(FaqRepository $faqRepository, TraineeRepository $traineeRepository, TrainerRepository $trainerRepository): Response
    {
        $sectorId = null;
        if($this->isGranted('ROLE_TRAINEE') === true) {
            $sectorId = $traineeRepository->findOneBy(["username" => $this->getUser()->getUserIdentifier()])->getCohort()->getTrainer()->getSector()->getId();
        } elseif($this->isGranted('ROLE_TRAINER') === true) {
            $sectorId = $trainerRepository->findOneBy(["username" => $this->getUser()->getUserIdentifier()])->getSector()->getId();
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
    #[Route('/survey', name: 'app_survey', methods: "GET")]
    public function survey(SurveyRepository $surveyRepository): Response
    {

        return $this->render('survey/index.html.twig', [
            'questions' => $surveyRepository->findAll()
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_TRAINER") or is_granted("ROLE_TRAINEE")'))]
    #[Route('/search', name: 'app_search', methods: ["GET", "POST"])]
    public function search(Request $request, SerializerInterface $serializer, CourseRepository $courseRepository, TraineeRepository $traineeRepository, TrainerRepository $trainerRepository): Response
    {
        if ($request->isXmlHttpRequest()) {
            $search = $request->request->get('q');

            $courses = $courseRepository->searchCourses($search);
            $trainees = $traineeRepository->searchTrainees($search);
            $trainers = $trainerRepository->searchTrainers($search);
            return $this->json(
                [
                    'success' => true,
                    'courses' => json_decode($serializer->serialize($courses, 'json', ['groups' => ['course_search']]), true),
                    'trainees' => json_decode($serializer->serialize($trainees, 'json', ['groups' => ['trainee_search']]), true),
                    'trainers' => json_decode($serializer->serialize($trainers, 'json', ['groups' => ['trainer_search']]), true),
                ],
                status: 200
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
