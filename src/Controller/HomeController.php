<?php

namespace App\Controller;

use App\Repository\CourseModuleRepository;
use App\Repository\CourseRepository;
use App\Repository\FaqRepository;
use App\Repository\SessionRepository;
use App\Repository\SurveyRepository;
use App\Repository\SurveyTraineeRepository;
use App\Repository\TraineeRepository;
use App\Repository\TrainerRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

class HomeController extends AbstractController
{
    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/', name: 'app_home', methods: "GET")]
    public function home(TraineeRepository $traineeRepository, CourseRepository $courseRepository, SurveyTraineeRepository $surveyTraineeRepository): Response
    {
        if ($this->isGranted('ROLE_USER') === true) {
            return $this->render('home/index.html.twig', [
                'satisfactionSurvey' => $traineeRepository->getSessionsInformations($this->getUser()->getUserIdentifier()),
                'latestCourses' => $courseRepository->getLastCourses($this->getUser()->getUserIdentifier()),
                'popularCourses' => $courseRepository->getPopularCourses(),
                'traineesOpinions' => $surveyTraineeRepository->getGlobalSurveys($this->getUser()->getUserIdentifier()),
            ]);
        } else {
            return $this->redirectToRoute('app_login');
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
    public function modules(CourseModuleRepository $courseModuleRepository): Response
    {
        return $this->render('course/module.html.twig', [
            'listModules' => $courseModuleRepository->findAll()
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/course/{course}/{search}', name: 'app_course', methods: "GET")]
    public function course(CourseRepository $courseRepository, string $course, string $search = null): Response
    {
        return $this->render('course/course.html.twig', [
            'listCourses' => $courseRepository->getCoursesInformations($course, $search)
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/embed/{slide}', name: 'app_embed', methods: "GET")]
    public function embed(CourseRepository $courseRepository, TraineeRepository $traineeRepository, string $slide): Response
    {
        if ($this->isGranted('ROLE_TRAINEE') === true) {
            $traineeRepository->updateCourseFollowed($slide, $this->getUser()->getUserIdentifier());
        }
        return $this->render('course/embed.html.twig', [
            'course' => $courseRepository->getCourseInformations($slide),
            'listResources' => [],
            'listTps' => [],
            'listExercise' => []
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_TRAINER")'))]
    #[Route('/admin', name: 'app_admin_dashboard', methods: "GET")]
    public function adminDashboard(): Response
    {
        return $this->render('home/index.html.twig', []);
    }

    #[IsGranted(new Expression('is_granted("ROLE_TRAINER") or is_granted("ROLE_TRAINEE")'))]
    #[Route('/mailbox', name: 'app_mailbox', methods: "GET")]
    public function mailbox(SessionRepository $testRepository, TraineeRepository $traineeRepository, TrainerRepository $trainerRepository): Response
    {
        $accounts = [];
        $sessions = [];
        if ($this->isGranted('ROLE_TRAINER')) {
            // die('trainer');
        } elseif ($this->isGranted('ROLE_TRAINEE')) {
            // die('trainee');
        }
        return $this->render('mailbox/index.html.twig', [
            'sessions' => $testRepository->getSessions(),
            'accounts' => $accounts
        ]);
    }

    #[Route('/faq', name: 'app_faq', methods: "GET")]
    public function faq(FaqRepository $faqRepository): Response
    {
        return $this->render('faq/index.html.twig', [
            'listThemes' => $faqRepository->getThemes(),
            'listFaqs' => $faqRepository->getFaqs()
        ]);
    }

    #[Route('/survey', name: 'app_survey', methods: "GET")]
    public function survey(SurveyRepository $surveyRepository): Response
    {

        return $this->render('survey/index.html.twig', [
            'questions' => $surveyRepository->findAll()
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/account', name: 'app_account', methods: "GET")]
    public function account(): Response
    {
        return $this->render('faq/index.html.twig', []);
    }
}
