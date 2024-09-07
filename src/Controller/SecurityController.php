<?php

namespace App\Controller;

use App\Entity\Avatar;
use App\Entity\User;
use App\Repository\AvatarRepository;
use App\Repository\InternshipRepository;
use App\Repository\TraineeRepository;
use App\Repository\TrainerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Config\FrameworkConfig;

#[Route(path: '/{_locale}')]
class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, SessionInterface $session): Response
    {
        if ($this->getUser()) {
            // if (!$session->has('session')) {
            //     $session->set('session', 'test');
            // }    
            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/recovery', name: 'app_recovery', methods: ['GET', 'POST'])]
    public function recovery(MailerInterface $mailer, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        if (!empty($request->getPayload()->get('form_forgotten_username'))) {
            $user = $userRepository->findOneBy(['username' => $request->getPayload()->get('form_forgotten_username')]);
            if (isset($user) && !empty($user) && $user instanceof User) {
                $user->setTmpCode(mt_rand(100000, 999999));
                $user->setTmpCodeDate(new \DateTimeImmutable('+7 days'));
                $entityManager->persist($user);
                $entityManager->flush($user);
                $email = (new Email())
                    ->from('no-reply@marceau-rodrigues.fr')
                    ->subject('Votre code temporaire de récupération de mot de passe');
                if ($_ENV['APP_ENV'] === 'prod') {
                    $email->to($user->getEmail());
                } elseif ($_ENV['APP_ENV'] === 'preprod' || $_ENV['APP_ENV'] === 'dev') {
                    $email->to('contact@marceau-rodrigues.fr');
                }
                $email->html($this->renderView('_emails/MAIL_CODE_TMP.html.twig', [
                    'username' => $user->getUsername(),
                    'website' => $_SERVER['SERVER_NAME'],
                    'tmpCode' => $user->getTmpCode(),
                    'expiration_date' => $user->getTmpCodeDate()->format('d/m/Y H:i:s'),
                ]));

                $mailer->send($email);
                return $this->redirectToRoute('app_code', ['code' => null]);
            }
        }

        return $this->render('security/recovery.html.twig');
    }

    // Requirements permet de laisser la possibilité de ne pas donner de code par défaut
    #[Route(path: '/code/{code}', name: 'app_code', requirements: ['code' => '\w*'], methods: ['GET', 'POST'])]
    public function code(string $code = null, Request $request, UserRepository $userRepository): Response
    {
        if (!empty($request->getPayload()->get('form_signup_code'))) {
            $user = $userRepository->findOneBy(['tmpCode' => $request->getPayload()->get('form_signup_code')]);
            if (isset($user) && !empty($user) && $user instanceof User) {
                return $this->redirectToRoute('app_signup', ['uuid' => $user->getUuid(), 'code' => $user->getTmpCode()]);
            } else {
                $this->addFlash(
                    'error',
                    'Une erreur s\'est produite, veuillez réessayer !'
                );
            }
        }

        return $this->render('security/code.html.twig', ['code' => $code]);
    }

    #[Route(path: '/signup/{uuid}/{code}', name: 'app_signup', methods: ['GET', 'POST'])]
    public function signup(string $uuid, string $code, MailerInterface $mailer, Request $request, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        if (!empty($request->getPayload()->get('form_signup_password'))) {
            $user = $userRepository->findOneBy(['tmpCode' => $code, 'uuid' => $uuid]);
            if (isset($user) && !empty($user) && $user instanceof User) {
                $user->setTmpCode(null);
                $user->setTmpCodeDate(null);
                $user->setPassword($userPasswordHasher->hashPassword(
                    $user,
                    $request->getPayload()->get('form_signup_password')
                ));
                $entityManager->persist($user);
                $entityManager->flush($user);

                $email = (new Email())
                    ->from('no-reply@marceau-rodrigues.fr')
                    ->subject('Définition d\'un nouveau mot de passe sur votre compte');
                if ($_ENV['APP_ENV'] === 'prod') {
                    $email->to($user->getEmail());
                } elseif ($_ENV['APP_ENV'] === 'dev') {
                    $email->to('contact@marceau-rodrigues.fr');
                }
                # TODO: Add a twig template for the email
                $email->html("Un nouveau mdp a été défini sur votre compte !");

                $mailer->send($email);
                $this->addFlash(
                    'notice',
                    'Votre mot de passe a bien été modifié !'
                );

                return $this->redirectToRoute('app_login');
            } else {
                $this->addFlash(
                    'error',
                    'Une erreur est survenue lors de la modification de votre mot de passe !'
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
    #[Route('/account', name: 'app_account', methods: ["GET", "POST"])]
    public function account(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, AvatarRepository $avatarRepository, InternshipRepository $internshipReposidtory, TrainerRepository $trainerRepository, TraineeRepository $traineeRepository): Response
    {
        $form = null;
        $trainer = $trainerRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $trainee = $traineeRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $user = ($trainer !== null ? $trainer : $trainee);
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

        return $this->render('security/account.html.twig', [
            'avatars' => $avatarRepository->findAll(),
            'trainer' => $trainer,
            'trainee' => $trainee,
            'user' => $user,
            'cohort' => ($trainee !== null ? $traineeRepository->getCohortsInformations($trainee->getUserIdentifier()) : []),
            'documents' => ($trainee !== null ? $traineeRepository->getCohortsInformations($trainee->getUserIdentifier())['documents'] : []),
            'internships' => ($trainee !== null ? $internshipReposidtory->findBy(['trainee' => $trainee->getId()]) : []),
            'surveys' => [],
            'form' => $form,
        ]);
    }
}
