<?php

namespace App\Controller\Admin;

use App\Entity\Avatar;
use App\Entity\Cohort;
use App\Entity\CohortInternship;
use App\Entity\Course;
use App\Entity\CourseCohort;
use App\Entity\CourseModule;
use App\Entity\Faq;
use App\Entity\Internship;
use App\Entity\Notification;
use App\Entity\Prospect;
use App\Entity\Quiz;
use App\Entity\QuizRow;
use App\Entity\QuizShare;
use App\Entity\QuizTheme;
use App\Entity\SiteSettings;
use App\Entity\Trainee;
use App\Entity\Trainer;
use App\Entity\User;
use App\Entity\UserQuiz;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    private EntityManagerInterface $entityManager;
    private Packages $packages;

    // Permet de récupérer l'entity manager pour pouvoir faire des requêtes en base de données
    public function __construct(EntityManagerInterface $entityManager, Packages $packages)
    {
        $this->entityManager = $entityManager;
        $this->packages = $packages;
    }

    #[Route('/{_locale}/admin/v2', name: 'admin')]
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
        $trainer = $this->entityManager->getRepository(Trainer::class)->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $trainersCohorts = [];
        $activeCohorts = $unactiveCohorts = $incomingCohorts = [];
        foreach ($this->entityManager->getRepository(Trainer::class)->findBy(['sector' => $trainer->getSector()]) as $trainer) {
            $trainersCohorts[] = $trainer->getCohorts();
            foreach ($trainer->getCohorts() as $cohort) {
                if (new \DateTimeImmutable() > $cohort->getStartDate() && new \DateTimeImmutable() < $cohort->getFinishDate()) {
                    $activeCohorts[] = $cohort;
                } elseif (new \DateTimeImmutable() < $cohort->getStartDate()) {
                    $incomingCohorts[] = $cohort;
                } else {
                    $unactiveCohorts[] = $cohort;
                }
            }
        }

        return $this->render('admin/dashboard.html.twig', [
            'activeCohorts' => $activeCohorts,
            'unactiveCohorts' => $unactiveCohorts,
            'incomingCohorts' => $incomingCohorts,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        $siteSettings = $this->entityManager->getRepository(SiteSettings::class)->find(1);
        return Dashboard::new()
            ->setTitle(strtoupper($siteSettings->getPlatformName()))
            ->setTranslationDomain('admin');
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addCssFile('css/admin/fields/trainee_internships.css');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        $user = $this->entityManager->getRepository(Trainer::class)->findOneBy(['username' => $user->getUserIdentifier()]);

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
                MenuItem::linkToRoute('My Profile', 'fa fa-id-card', '...', ['...' => '...']),
                MenuItem::linkToRoute('Settings', 'fa fa-user-cog', '...', ['...' => '...']),
                MenuItem::section('Langage', 'fa fa-flag'),
                MenuItem::linkToRoute('Français', null, '...', ['_locale' => 'en']),
                MenuItem::linkToRoute('Anglais', null, '...', ['...' => '...']),
            ]);
    }

    public function configureMenuItems(): iterable
    {
        $siteSettings = $this->entityManager->getRepository(SiteSettings::class)->find(1);

        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Users');
        // yield MenuItem::linkToCrud('Users', 'fa fa-user', User::class);
        yield MenuItem::linkToCrud('Trainees', 'fa fa-graduation-cap', Trainee::class);
        yield MenuItem::linkToCrud('Trainers', 'fa fa-user-tie', Trainer::class);
        yield MenuItem::linkToCrud('Avatars', 'fa fa-face-smile', Avatar::class);

        yield MenuItem::section('Courses');
        yield MenuItem::linkToCrud('Modules', 'fa fa-book', CourseModule::class);
        yield MenuItem::linkToCrud('Courses', 'fa fa-book', Course::class);
        yield MenuItem::linkToCrud('Cohorts', 'fa fa-book', CourseCohort::class);

        yield MenuItem::section('Quizzes');
        yield MenuItem::linkToCrud('Quizzes', 'fa fa-book', Quiz::class);
        yield MenuItem::linkToCrud('Questions', 'fa fa-book', QuizRow::class);
        yield MenuItem::linkToCrud('Shared', 'fa fa-book', QuizShare::class);
        yield MenuItem::linkToCrud('Theme', 'fa fa-book', QuizTheme::class);
        yield MenuItem::linkToCrud('Answers', 'fa fa-comments', UserQuiz::class);

        yield MenuItem::section('Internships');
        yield MenuItem::linkToCrud('Cohort', 'fa fa-briefcase', CohortInternship::class);
        yield MenuItem::linkToCrud('Prospects', 'fa fa-briefcase', Prospect::class);
        yield MenuItem::linkToCrud('Internships', 'fa fa-briefcase', Internship::class);
        yield MenuItem::linkToCrud('Trainees', 'fa fa-briefcase', Trainee::class)
            ->setController(TraineeInternshipCrudController::class);

        yield MenuItem::section('Other');
        if($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::linkToCrud('Application', 'fa fa-sliders', SiteSettings::class);
        }
        yield MenuItem::linkToCrud('Notifications', 'fa fa-bell', Notification::class);
        yield MenuItem::linkToCrud('FAQ', 'fa fa-question', Faq::class);

        yield MenuItem::section('');
        yield MenuItem::linkToRoute('Back to ' . strtoupper($siteSettings->getPlatformName()), 'fa-solid fa-backward-step', 'app_home');
        yield MenuItem::linkToLogout('Logout', 'fa fa-sign-out');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
