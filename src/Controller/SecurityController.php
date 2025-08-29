<?php

namespace App\Controller;

use App\Entity\Avatar;
use App\Entity\User;
use App\Repository\AvatarRepository;
use App\Repository\CohortInternshipRepository;
use App\Repository\CoordinatorRepository;
use App\Repository\CourseCohortRepository;
use App\Repository\CourseTraineeRepository;
use App\Repository\InternshipRepository;
use App\Repository\ResponsibleRepository;
use App\Repository\SiteSettingsRepository;
use App\Repository\TraineeInternshipRepository;
use App\Repository\TraineeRepository;
use App\Repository\TraineeResourceRepository;
use App\Repository\TrainerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/{_locale}')]
class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, SessionInterface $session): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/recovery', name: 'app_recovery', methods: ['GET', 'POST'])]
    public function recovery(MailerInterface $mailer, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, SiteSettingsRepository $siteSettingsRepository, TranslatorInterface $translator): Response
    {
        if (!empty($request->getPayload()->get('form_forgotten_username'))) {
            $site = $siteSettingsRepository->findOneBy(['id' => 1]);
            $user = $userRepository->findOneBy(['username' => $request->getPayload()->get('form_forgotten_username')]);
            if (isset($user) && !empty($user) && $user instanceof User) {
                $user->setTmpCode(mt_rand(100000, 999999));
                $user->setTmpCodeDate(new \DateTimeImmutable('+7 days'));
                $entityManager->persist($user);
                $entityManager->flush();
                $email = (new Email())
                    ->from('no-reply@marceau-rodrigues.fr')
                    ->subject($translator->trans('recovery.subject', [], 'email'));
                if ($_ENV['APP_ENV'] === 'prod' || $_ENV['APP_ENV'] === 'test') {
                    $email->to($user->getEmail());
                } elseif ($_ENV['APP_ENV'] === 'preprod' || $_ENV['APP_ENV'] === 'dev') {
                    $email->to('contact@marceau-rodrigues.fr');
                }
                $email->html($this->renderView('_emails/MAIL_CODE_TMP.html.twig', [
                    'TITLE' => $translator->trans('recovery.subject', [], 'email'),
                    'GREETING' => $translator->trans('recovery.greeting', ['%name%' => $user->getUsername()], 'email'),
                    'MESSAGE' => $translator->trans('recovery.body', [], 'email'),
                    'EXPIRATION' => $translator->trans('recovery.expiration', ['%expiration%' => $user->getTmpCodeDate()->format($translator->trans('global.date_format.long'))], 'email'),
                    'TMP_CODE' => $user->getTmpCode(),
                    'PLATFORM_LOGO_NAME' => $site->getLogoName(),
                    'PLATFORM_LOGO' => $site->getLogoPath(),
                    'PLATFORM_NAME' => $site->getPlatformName(),
                    'WEBSITE' => $_SERVER['SERVER_NAME'],
                    'BTN_TEXT' => $translator->trans('recovery.button', [], 'email'),
                    'CLOSING' => $translator->trans('recovery.closing', [], 'email'),
                    'SIGNATURE' => $translator->trans('recovery.signature', [], 'email'),
                    'FOOTER' => $translator->trans('recovery.footer', [], 'email'),
                ]));

                $mailer->send($email);
                $this->addFlash(
                    'info',
                    $translator->trans('recovery.info', [], 'email')
                );
                return $this->redirectToRoute('app_code', ['code' => null]);
            }
            $this->addFlash(
                'error',
                $translator->trans('recovery.error', [], 'email')
            );
        }

        return $this->render('security/recovery.html.twig');
    }

    // Requirements permet de laisser la possibilité de ne pas donner de code par défaut
    #[Route(path: '/code/{code}', name: 'app_code', requirements: ['code' => '\w*'], methods: ['GET', 'POST'])]
    public function code(Request $request, UserRepository $userRepository, ?string $code = null): Response
    {
        if (!empty($request->getPayload()->get('form_signup_code'))) {
            $user = $userRepository->findOneBy(['tmpCode' => $request->getPayload()->get('form_signup_code')]);
            if (isset($user) && !empty($user) && $user instanceof User) {
                return $this->redirectToRoute('app_signup', ['uuid' => $user->getUuid(), 'code' => $user->getTmpCode()]);
            } else {
                $this->addFlash(
                    'error',
                    new TranslatableMessage('global.exceptions.other')
                );
            }
        }

        return $this->render('security/code.html.twig', ['code' => $code]);
    }

    #[Route(path: '/signup/{uuid}/{code}', name: 'app_signup', methods: ['GET', 'POST'])]
    public function signup(MailerInterface $mailer, Request $request, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository, EntityManagerInterface $entityManager, SiteSettingsRepository $siteSettingsRepository, TranslatorInterface $translator, string $uuid, string $code): Response
    {
        if (!empty($request->getPayload()->get('form_signup_password'))) {
            $site = $siteSettingsRepository->findOneBy(['id' => 1]);
            $user = $userRepository->findOneBy(['tmpCode' => $code, 'uuid' => $uuid]);
            if (isset($user) && !empty($user) && $user instanceof User) {
                $user->setTmpCode(null);
                $user->setTmpCodeDate(null);
                $user->setPassword($userPasswordHasher->hashPassword(
                    $user,
                    $request->getPayload()->get('form_signup_password')
                ));

                $email = (new Email())
                    ->from('contact@marceau-rodrigues.fr')
                    ->subject($translator->trans('signup.subject', [], 'email'));
                if ($_ENV['APP_ENV'] === 'prod' || $_ENV['APP_ENV'] === 'test') {
                    $email->to($user->getEmail());
                } elseif ($_ENV['APP_ENV'] === 'dev') {
                    $email->to('contact@marceau-rodrigues.fr');
                }
                $email->html($this->renderView('_emails/MAIL_PASSWORD_CHANGED.html.twig', [
                    'TITLE' => $translator->trans('signup.subject', [], 'email'),
                    'GREETING' => $translator->trans('signup.greeting', ['%name%' => $user->getUsername()], 'email'),
                    'MESSAGE' => $translator->trans('signup.body', [], 'email'),
                    'PLATFORM_LOGO_NAME' => $site->getLogoName(),
                    'PLATFORM_LOGO' => $site->getLogoPath(),
                    'PLATFORM_NAME' => $site->getPlatformName(),
                    'WEBSITE' => $_SERVER['SERVER_NAME'],
                    'BTN_TEXT' => $translator->trans('signup.button', [], 'email'),
                    'CLOSING' => $translator->trans('signup.closing', [], 'email'),
                    'SIGNATURE' => $translator->trans('signup.signature', [], 'email'),
                    'CONTACT_EMAIL' => 'contact@marceau-rodrigues.fr',
                ]));

                $mailer->send($email);
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash(
                    'notice',
                    $translator->trans('signup.success', [], 'email')
                );

                return $this->redirectToRoute('app_login');
            } else {
                $this->addFlash(
                    'error',
                    $translator->trans('signup.error', [], 'email')
                );
            }
        }
        return $this->render('security/signup.html.twig', [
            'uuid' => $uuid,
            'code' => $code,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/account/privacy', name: 'app_account_privacy_index', methods: ["GET", "POST"])]
    public function privacy(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, AvatarRepository $avatarRepository, ResponsibleRepository $responsibleRepository, CoordinatorRepository $coordinatorRepository, TrainerRepository $trainerRepository, TraineeRepository $traineeRepository): Response
    {
        $form = null;
        $responsible = $responsibleRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $coordinator = $coordinatorRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $trainer = $trainerRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $trainee = $traineeRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $user = ($responsible !== null ? $responsible : ($coordinator !== null ? $coordinator : ($trainer !== null ? $trainer : $trainee)));
        $form = $this->createFormBuilder($user)
            ->add('username', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => true,
            ])
            ->add('lastName', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => true,
            ])
            ->add('firstName', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => true,
            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false,
                'mapped' => false
            ])
            ->add('email', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => true,
            ])
            ->add('phoneNumber', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false
            ])
            ->add('signature', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false,
            ])
            ->add('avatar', EntityType::class, [
                'class' => Avatar::class,
                'choice_label' => 'label',
                'choice_attr' => function ($choice, string $key, mixed $value) {
                    // adds a class like attending_yes, attending_no, etc
                    return ['data-src' => $choice->getLink()];
                },
                'attr' => [
                    'class' => 'form-select',
                ],
                'required' => true,
            ])
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success',
                    'label' => 'S\'inscrire'
                ]
            ])->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('password')->getData() !== null && !empty($form->get('password')->getData())) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
            }
            $this->addFlash(
                'notice',
                'Vos changements ont bien été sauvegardés !'
            );
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/privacy.html.twig', [
            'avatars' => $avatarRepository->findAll(),
            'responsible' => $responsible,
            'coordinator' => $coordinator,
            'trainer' => $trainer,
            'trainee' => $trainee,
            'user' => $user,
            'surveys' => [],
            'form' => $form,
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/account/favorite-courses', name: 'app_account_favorite_courses_index', methods: ["GET", "POST"])]
    public function favoriteCourses(TraineeRepository $traineeRepository): Response
    {
        $trainee = $traineeRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);

        return $this->render('account/favoriteCourses.html.twig', [
            'trainee' => $trainee,
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_TRAINER")'))]
    #[Route('/account/created-sandboxes', name: 'app_account_created_sandboxes_index', methods: ["GET", "POST"])]
    public function createdSandboxes(TrainerRepository $trainerRepository): Response
    {
        $sandboxes = $trainerRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()])->getSandboxes();

        return $this->render('account/createdSandboxes.html.twig', [
            'sandboxes' => $sandboxes,
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/account/informations-cohort', name: 'app_account_informations_cohort_index', methods: ["GET", "POST"])]
    public function informationsCohort(TrainerRepository $trainerRepository, TraineeRepository $traineeRepository): Response
    {
        $form = null;
        $trainer = $trainerRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $trainee = $traineeRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $user = ($trainer !== null ? $trainer : $trainee);

        return $this->render('account/informationsCohort.html.twig', [
            'trainer' => $trainer,
            'trainee' => $trainee,
            'user' => $user,
            'cohort' => ($trainee !== null ? $traineeRepository->getCohortsInformations($trainee->getUserIdentifier()) : []),
            'form' => $form,
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/account/documents', name: 'app_account_documents_index', methods: ["GET", "POST"])]
    public function documents(CohortInternshipRepository $cohortInternshipRepository, TraineeInternshipRepository $traineeInternshipRepository, InternshipRepository $internshipReposidtory, TrainerRepository $trainerRepository, TraineeRepository $traineeRepository): Response
    {
        $trainer = $trainerRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $trainee = $traineeRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $user = ($trainer !== null ? $trainer : $trainee);

        return $this->render('account/documents.html.twig', [
            'trainer' => $trainer,
            'trainee' => $trainee,
            'user' => $user,
            'cohort' => ($trainee !== null ? $traineeRepository->getCohortsInformations($trainee->getUserIdentifier()) : []),
            'documents' => ($trainee !== null ? $traineeRepository->getCohortsInformations($trainee->getUserIdentifier())['documents'] : []),
            'internships' => ($trainee !== null ? $internshipReposidtory->findBy(['trainee' => $trainee->getId()]) : []),
            'internships_periods' => ($trainee !== null ? $cohortInternshipRepository->findBy(['cohort' => $trainee->getCohort()]) : []),
            'internships_trainee' => ($trainee !== null ? $traineeInternshipRepository->findBy(['trainee' => $trainee]) : []),
            // 'internships' => ($trainee !== null ? $traineeInternshipRepository->findBy(['trainee' => $trainee->getId()]) : []),
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/account/statistics', name: 'app_account_statistics_index', methods: ["GET", "POST"])]
    public function statistics(CourseTraineeRepository $courseTraineeRepository, CourseCohortRepository $courseCohortRepository, TraineeResourceRepository $traineeResourceRepository, TrainerRepository $trainerRepository, TraineeRepository $traineeRepository): Response
    {
        $trainer = $trainerRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $trainee = $traineeRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $user = ($trainer !== null ? $trainer : $trainee);

        // Récupération des informations pour les graphiques
        $graphDataCourses = [];
        $graphDataHomeworks = [];
        if ($this->isGranted('ROLE_TRAINEE')) {
            $graphDataCourses['value_two'] = sizeof($courseTraineeRepository->findBy(['trainee' => $trainee->getId()]));
            $graphDataCourses['value_one'] = sizeof($courseCohortRepository->findBy(['cohort' => $trainee->getCohort()->getId()])) - $graphDataCourses['value_two'];
            $graphDataHomeworks['value_one'] = 0;
            $graphDataHomeworks['value_two'] = 0;
            foreach ($trainee->getCohort()->getCourseCohorts() as $courseUnlocked) {
                foreach ($courseUnlocked->getCourse()->getCourseResources() as $resource) {
                    if ($resource->getType() === 'tp') {
                        $graphDataHomeworks['value_one']++;
                        $graphDataHomeworks['value_two'] += sizeof($traineeResourceRepository->findBy(['trainee' => $trainee->getId(), 'courseResource' => $resource->getId()]));
                    }
                }
            }
            $graphDataHomeworks['value_one'] = $graphDataHomeworks['value_one'] - $graphDataHomeworks['value_two'];
        }


        return $this->render('account/statistics.html.twig', [
            'trainer' => $trainer,
            'trainee' => $trainee,
            'user' => $user,
            'graphDataCourses' => $graphDataCourses, // TODO: vérifier les données retournées
            'graphDataHomeworks' => $graphDataHomeworks,
        ]);
    }
}
