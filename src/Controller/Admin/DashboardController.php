<?php

namespace App\Controller\Admin;

use App\Entity\Avatar;
use App\Entity\Cohort;
use App\Entity\CohortInternship;
use App\Entity\Coordinator;
use App\Entity\Course;
use App\Entity\CourseCohort;
use App\Entity\CourseModule;
use App\Entity\CourseResource;
use App\Entity\ExportParameter;
use App\Entity\Faq;
use App\Entity\Internship;
use App\Entity\Notification;
use App\Entity\Prospect;
use App\Entity\Quiz;
use App\Entity\QuizRow;
use App\Entity\QuizShare;
use App\Entity\QuizTheme;
use App\Entity\Responsible;
use App\Entity\Sandbox;
use App\Entity\Sector;
use App\Entity\SiteSettings;
use App\Entity\Survey;
use App\Entity\SurveyTrainee;
use App\Entity\Trainee;
use App\Entity\TraineeResource;
use App\Entity\Trainer;
use App\Entity\User;
use App\Entity\UserQuiz;
use App\Service\LocaleService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[IsGranted('ROLE_ADMIN')]
#[AdminDashboard(routePath: '{_locale}/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    private EntityManagerInterface $entityManager;
    private Packages $packages;
    private RequestStack $requestStack;
    private LocaleService $localeService;

    // Permet de récupérer l'entity manager pour pouvoir faire des requêtes en base de données
    public function __construct(EntityManagerInterface $entityManager, Packages $packages, RequestStack $requestStack, LocaleService $localeService)
    {
        $this->entityManager = $entityManager;
        $this->packages = $packages;
        $this->requestStack = $requestStack;
        $this->localeService = $localeService;
    }

    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        $responsible = $this->entityManager->getRepository(Responsible::class)->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $coordinator = $this->entityManager->getRepository(Coordinator::class)->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $trainer = $this->entityManager->getRepository(Trainer::class)->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $trainers = [];
        if ($responsible instanceof Responsible) {
            foreach ($responsible->getCoordinators() as $coordinators) {
                foreach ($coordinators->getTrainers() as $trainer) {
                    $trainers[] = $trainer;
                }
            }
        } elseif ($coordinator instanceof Coordinator) {
            foreach ($coordinator->getTrainers() as $trainer) {
                $trainers[] = $trainer;
            }
        } else {
            array_push($trainers, $trainer);
        }

        $trainersCohorts = [];
        $activeCohorts = $unactiveCohorts = $uncomingCohorts = [];
        foreach ($trainers as $trainer) {
            $trainersCohorts[] = $trainer->getCohorts();
            foreach ($trainer->getCohorts() as $cohort) {
                if (new \DateTimeImmutable() >= $cohort->getStartDate() && new \DateTimeImmutable() <= $cohort->getFinishDate()) {
                    $activeCohorts[] = $cohort;
                } elseif (new \DateTimeImmutable() < $cohort->getStartDate()) {
                    $uncomingCohorts[] = $cohort;
                } else {
                    $unactiveCohorts[] = $cohort;
                }
            }
        }

        return $this->render('admin/dashboard.html.twig', [
            'activeCohorts' => $activeCohorts,
            'unactiveCohorts' => $unactiveCohorts,
            'uncomingCohorts' => $uncomingCohorts,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        $siteSettings = $this->entityManager->getRepository(SiteSettings::class)->find(1);
        return Dashboard::new()
            ->setTitle('<img style="height:75px;" src="/website/' . $siteSettings->getLogoPath() . '">&nbsp;&nbsp;' . strtoupper($siteSettings->getPlatformName()))
            ->setTranslationDomain('admin');
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addCssFile('css/admin/fields/trainee_internships.css');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        $request = $this->requestStack->getCurrentRequest();
        $currentRoute = $request->attributes->get('_route'); // ex : 'admin_quiz_index'
        $currentRouteParams = $request->attributes->get('_route_params'); // tableau des paramètres actuels (id, page, etc.)
        $languageMenuItems = [];

        // Section langue
        $languageMenuItems[] = MenuItem::section('admin.menu.language.label', 'fa fa-flag');

        // Liens vers les autres langues
        foreach ($this->localeService->getLocaleSwitchUrls() as $locale => $data) {
            $languageMenuItems[] = MenuItem::linkToUrl('admin.'.$data['label'], null, $data['url']);
        }

        $responsible = $this->entityManager->getRepository(Responsible::class)->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $coordinator = $this->entityManager->getRepository(Coordinator::class)->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $trainer = $this->entityManager->getRepository(Trainer::class)->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $user = ($responsible !== null ? $responsible : ($coordinator !== null ? $coordinator : $trainer));

        if (!$user instanceof User) {
            throw new \Exception('Wrong user');
        }
        // Usually it's better to call the parent method because that gives you a
        // user menu with some menu items already created ("sign out", "exit impersonation", etc.)
        // if you prefer to create the user menu from scratch, use: return UserMenu::new()->...
        return parent::configureUserMenu($user)
            // use the given $user object to get the user name
            ->setName($user->getFirstName() . ' ' . $user->getLastName())
            // use this method if you don't want to display the name of the user
            ->displayUserName(true)

            // you can return an URL with the avatar image
            // ->setAvatarUrl('https://...')
            // Génère le lien de l'avatar de l'utilisateur avec le lien assets
            ->setAvatarUrl($this->packages->getUrl('../avatars/' . $user->getAvatar()->getLink()))
            // use this method if you don't want to display the user image
            ->displayUserAvatar(true)
            // you can also pass an email address to use gravatar's service
            // ->setGravatarEmail($user->getMainEmailAddress())

            // you can use any type of menu item, except submenus
            ->addMenuItems([
                MenuItem::linkToUrl('admin.menu.profile', 'fa fa-id-card', $this->generateUrl('app_account_privacy_index')),
                ...$languageMenuItems
            ]);
    }

    public function configureMenuItems(): iterable
    {
        $siteSettings = $this->entityManager->getRepository(SiteSettings::class)->find(1);

        yield MenuItem::linkToDashboard('admin.menu.dashboard', 'fa fa-home');

        yield MenuItem::section('admin.menu.users.label');
        // yield MenuItem::linkToCrud('Users', 'fa fa-user', User::class);
        yield MenuItem::linkToCrud('admin.menu.users.trainees', 'fa fa-graduation-cap', Trainee::class);
        yield MenuItem::linkToCrud('admin.menu.users.trainers', 'fa fa-user-tie', Trainer::class);
        if ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::linkToCrud('admin.menu.users.coordinators', 'fa fa-user-tie', Coordinator::class);
        }
        if ($this->isGranted('ROLE_RESPONSIBLE')) {
            yield MenuItem::linkToCrud('admin.menu.users.responsibles', 'fa fa-user-tie', Responsible::class);
            yield MenuItem::linkToCrud('admin.menu.users.sectors', 'fa fa-chart-pie', Sector::class);
        }
        yield MenuItem::linkToCrud('admin.menu.users.avatars', 'fa fa-face-smile', Avatar::class);
        yield MenuItem::linkToCrud('admin.menu.users.surveys', 'fa fa-clipboard-list', Survey::class);
        yield MenuItem::linkToCrud('admin.menu.users.surveys_trainees', 'fa fa-clipboard-list', SurveyTrainee::class);

        yield MenuItem::section('admin.menu.courses.label');
        yield MenuItem::linkToCrud('admin.menu.courses.cohorts', 'fa fa-people-group', Cohort::class);
        yield MenuItem::linkToCrud('admin.menu.courses.modules', 'fa fa-book', CourseModule::class);
        yield MenuItem::linkToCrud('admin.menu.courses.lessons', 'fa fa-book', Course::class);
        yield MenuItem::linkToCrud('admin.menu.courses.resources', 'fa fa-book', CourseResource::class);
        yield MenuItem::linkToCrud('admin.menu.courses.trainees_resources', 'fa fa-book', TraineeResource::class);
        yield MenuItem::linkToCrud('admin.menu.courses.shared', 'fa fa-book', CourseCohort::class);
        yield MenuItem::linkToCrud('admin.menu.courses.sandboxes', 'fa fa-chalkboard', Sandbox::class);

        yield MenuItem::section('admin.menu.quizzes.label');
        yield MenuItem::linkToCrud('admin.menu.quizzes.label', 'fa fa-book', Quiz::class);
        yield MenuItem::linkToCrud('admin.menu.quizzes.questions', 'fa fa-book', QuizRow::class);
        yield MenuItem::linkToCrud('admin.menu.quizzes.shared', 'fa fa-book', QuizShare::class);
        yield MenuItem::linkToCrud('admin.menu.quizzes.theme', 'fa fa-book', QuizTheme::class);
        yield MenuItem::linkToCrud('admin.menu.quizzes.answers', 'fa fa-comments', UserQuiz::class);

        yield MenuItem::section('admin.menu.internships.label');
        yield MenuItem::linkToCrud('admin.menu.internships.cohorts', 'fa fa-briefcase', CohortInternship::class);
        yield MenuItem::linkToCrud('admin.menu.internships.prospects', 'fa fa-briefcase', Prospect::class);
        yield MenuItem::linkToCrud('admin.menu.internships.label', 'fa fa-briefcase', Internship::class);
        yield MenuItem::linkToCrud('admin.menu.internships.trainees', 'fa fa-briefcase', Trainee::class)
            ->setController(TraineeInternshipCrudController::class);

        yield MenuItem::section('admin.menu.other');
        if ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::linkToCrud('admin.menu.application', 'fa fa-sliders', SiteSettings::class);
        }
        yield MenuItem::linkToCrud('admin.menu.export_parameters', 'fa fa-download', ExportParameter::class);
        yield MenuItem::linkToCrud('admin.menu.notifications', 'fa fa-bell', Notification::class);
        yield MenuItem::linkToCrud('admin.menu.q_and_A', 'fa fa-question', Faq::class);

        yield MenuItem::section('');
        yield MenuItem::linkToUrl(new TranslatableMessage('admin.menu.back', ['%app_name%' => strtoupper($siteSettings->getPlatformName())], 'admin'), 'fa-solid fa-backward-step', $this->generateUrl('app_home'));
        yield MenuItem::linkToLogout('admin.menu.logout', 'fa fa-sign-out');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
