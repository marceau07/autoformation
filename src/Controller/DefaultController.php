<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\Course;
use App\Entity\Feedback;
use App\Entity\Message;
use App\Entity\Notification;
use App\Entity\SiteSettings;
use App\Entity\TraineeCourseFavorite;
use App\Entity\TraineeInternship;
use App\Entity\TraineeResource;
use App\Repository\CalendarRepository;
use App\Repository\CohortInternshipRepository;
use App\Repository\CohortRepository;
use App\Repository\CourseModuleRepository;
use App\Repository\CourseRepository;
use App\Repository\CourseResourceRepository;
use App\Repository\FeedbackCategoryRepository;
use App\Repository\MessageRepository;
use App\Repository\NotificationRepository;
use App\Repository\SiteSettingsRepository;
use App\Repository\TraineeCourseFavoriteRepository;
use App\Repository\TraineeInternshipRepository;
use App\Repository\TraineeRepository;
use App\Repository\TrainerRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
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

    #[Route('/css/colors.css', name: 'app_colors_css')]
    public function themeCss(SiteSettingsRepository $siteSettingsRepository): Response
    {
        $siteSettings = $siteSettingsRepository->find(1);

        // Si pas trouvé, on crée éventuellement un thème par défaut
        if (!$siteSettings) {
            $siteSettings = (new SiteSettings())
                ->setPrimaryColor('#FF0000')
                ->setSecondaryColor('#00FF00');
            // Pas forcément besoin de l'enregistrer,
            // c'est juste au cas où la BDD est vide
        }

        // Construire le contenu CSS
        // On veut créer nos variables CSS dans ":root"
        $cssContent = <<<CSS
            /* Définition des couleurs */
            :root {
                --coul-principale: {$siteSettings->getPrimaryColor()};
                --coul-secondaire: {$siteSettings->getSecondaryColor()};
                --coul-ternaire: {$siteSettings->getTertiaryColor()};
                --coul-quaternaire: {$siteSettings->getQuaternaryColor()};
                --coul-claire: {$siteSettings->getLightenColor()};
                --coul-foncee: {$siteSettings->getDarkenColor()};
            }
            CSS;

        // On prépare la réponse HTTP avec le bon mime type
        $response = new Response($cssContent);
        $response->headers->set('Content-Type', 'text/css');

        return $response;
    }

    /**
     * WIP
     */
    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/events', name: 'app_events', methods: "GET")]
    public function events(CalendarRepository $calendarRepository, TraineeRepository $traineeRepository, TrainerRepository $trainerRepository): JsonResponse
    {
        $data = [];
        if ($this->isGranted('ROLE_TRAINEE')) {
            $cohortId = $traineeRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()])->getCohort()->getId();
            $events = $calendarRepository->findBy(['cohort' => $cohortId], ['startDate' => 'ASC']);
        } elseif ($this->isGranted('ROLE_TRAINER')) {
            $trainerId = $trainerRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()])->getId();
            $events = $calendarRepository->findBy(['trainer' => $trainerId], ['startDate' => 'ASC']);
        }

        foreach ($events as $event) {
            $data[] = [
                'uuid' => $event->getUuid(),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'start' => $event->getStartDate()->format('Y-m-d H:i:s'),
                'end' => $event->getFinishDate()->format('Y-m-d H:i:s'),
            ];
        }

        return $this->json($data);
    }

    /**
     * WIP
     */
    #[IsGranted(new Expression('is_granted("ROLE_TRAINER")'))]
    #[Route('/events/new', name: 'app_events_add', methods: "POST")]
    public function addEvent(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $calendar = new Calendar();
        $calendar->setTitle($data['title']);
        $calendar->setDescription($data['description']);
        $startDate = new DateTimeImmutable($data['start']);
        $calendar->setStartDate($startDate);
        $endDate = new DateTimeImmutable($data['end']);
        $calendar->setFinishDate($endDate);
        $calendar->setCohort(null);
        $calendar->setTrainer($this->getUser());

        try {
            $entityManager->persist($calendar);
            $entityManager->flush();

            $this->addFlash('success', "L'événement a bien été ajouté !");
            return $this->json(
                [
                    'success' => true,
                    'message' => "L'événement a bien été ajouté !",
                ],
                status: Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            $this->addFlash('danger', "Erreur lors de l'ajout de l'événement...");
        }
        return $this->json(
            [
                'success' => false,
                'message' => "Erreur lors de l'ajout de l'événement...",
            ],
            status: Response::HTTP_BAD_REQUEST
        );
    }

    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/feedback', name: 'app_feedback', methods: "POST")]
    public function feedback(TranslatorInterface $translator, FeedbackCategoryRepository $feedbackCategoryRepository, SiteSettingsRepository $siteSettingsRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $siteSettings = $siteSettingsRepository->find(1);

        $feedback = new Feedback();
        $feedback->setCategory($feedbackCategoryRepository->find($request->request->get('form_feedback_category')));
        $feedback->setAnnotation($request->request->get('form_feedback_annotation'));
        $feedback->setLink($request->request->get('form_feedback_link'));
        $feedback->setWeight($request->request->get('form_feedback_weight'));
        $feedback->setUser($this->getUser());
        $entityManager->persist($feedback);
        $entityManager->flush();

        // Your GitHub personal access token (GPA)
        $accessToken = $_ENV['GITHUB_PERSONAL_ACCESS_TOKEN'];

        // The repository owner and repo name
        $owner = 'marceau07';
        $repo = $_ENV['GITHUB_PERSONAL_REPOSITORY'];

        $platformName = strtoupper($siteSettings->getPlatformName());

        // The data for the issue (title, body, etc.)
        $data = [
            'title' => "[" . $platformName . "-" . $feedback->getId() . "_" . $_ENV['APP_ENV'] . "]",
            'body' =>
            "# [" . $platformName . "-" . $feedback->getId() . "_" . $_ENV['APP_ENV'] . "] " . mb_substr(trim(preg_replace('/\s+/', '...', $feedback->getCategory()->getLabel())), 0, 200) . "\n\n"
                . $_SERVER['HTTP_USER_AGENT'] . "\n\n\n"
                . $feedback->getAnnotation() . "\n\n"
                . "[`link`](<" . $feedback->getLink() . ">)\n\n"
                . "Gravity: " . $feedback->getWeight() . "/4\n\n"
                . "Found by @" . $this->getUser()->getUserIdentifier() . "\n"
                . "Generated by " . $platformName,
            'assignees' => ['marceau07'],
            'labels' => [$_ENV['APP_ENV'], ($feedback->getCategory()->getLabel() == "Problème" ? "bug" : ($feedback->getCategory()->getLabel() == "Manque" ? "help wanted" : "enhancement"))]
        ];

        // Convert data to JSON format
        $jsonData = json_encode($data);

        // Set up cURL
        $ch = curl_init("https://api.github.com/repos/$owner/$repo/issues");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP GitHub Issue Creator');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/vnd.github+json'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        // Execute the request
        $response = curl_exec($ch);
        $message = '';
        if (!$response) {
            $message = 'Error:' . curl_error($ch);
        } else {
            $message = 'Response:' . $response;
        }

        // Close the cURL session
        curl_close($ch);

        if (!empty($message_bis)) {
            $this->addFlash('error', $translator->trans('global.error', [], null, $request->getLocale()));
            return $this->json(
                [
                    'success' => false,
                    'message' => $message,
                ],
                status: Response::HTTP_BAD_REQUEST
            );
        }
        $this->addFlash('info', $translator->trans('global.message_sent', [], null, $request->getLocale()));
        return $this->json(
            [
                'success' => true,
            ],
            status: Response::HTTP_OK
        );
    }

    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/{_locale}/chatbot', name: 'app_chatbot', methods: "POST")]
    public function chatbot(TranslatorInterface $translator, Packages $assets, TraineeRepository $traineeRepository, TrainerRepository $trainerRepository, CourseRepository $courseRepository, Request $request): Response
    {
        $userMessage = strtolower(trim($request->request->get('form_chatbot_message')));

        if (substr($userMessage, 0, 1) === "/") {
            $userMessage = substr($userMessage, 1);
            $closest = [];
            switch ($userMessage) {
                case strpos($userMessage, $translator->trans('chatbot.keys.courses', [], null, $request->getLocale())) === 0:
                    $keywords = explode($translator->trans('chatbot.keys.courses', [], null, $request->getLocale()) . ' ', trim($userMessage))[1];
                    $courses = $courseRepository->findOneBy(['link' => $keywords]) ?? $courseRepository->searchCourses($keywords);
                    if (!empty($courses)) {
                        array_push($closest, $translator->trans('chatbot.found', ['%subject%' => $translator->trans('chatbot.keys.courses', [], null, $request->getLocale()), '%keywords%' => $keywords], null, $request->getLocale()));
                        foreach ($courses as $course) {
                            array_push(
                                $closest,
                                '<div>
                                    <h6 class="fw-bold fs-5 ">[' . $course->getModule()->getLabel() . ']&nbsp;' . $course->getTitle() . '</h6>
                                    <p class="fs-6">' . $course->getSynopsis() . '</p>
                                    <a class="btn btn-primary text-center" href="/' . $request->getLocale() . '/embed/' . $course->getLink() . '">' . $translator->trans('global.btn_consult', [], null, $request->getLocale()) . '</a>
                                </div>'
                            );
                        }
                    } else {
                        $closest = [$translator->trans('chatbot.not_found', ['%keywords%' => $keywords], null, $request->getLocale())];
                    }
                    break;
                case strpos($userMessage, $translator->trans('chatbot.keys.modules', [], null, $request->getLocale())) === 0:
                    $keywords = explode($translator->trans('chatbot.keys.modules', [], null, $request->getLocale()) . ' ', trim($userMessage))[1];
                    if ($this->isGranted('ROLE_TRAINER')) {
                        $courses = $courseRepository->getCoursesInformationsBySector($keywords);
                        if (empty($courses)) {
                            $courses = $courseRepository->getCoursesInformationsBySector(null, $keywords);
                        }
                    } else {
                        $courses = $courseRepository->getCoursesInformationsByCohort($this->getUser()->getUserIdentifier(), $keywords);
                        if (empty($courses)) {
                            $courses = $courseRepository->getCoursesInformationsByCohort($this->getUser()->getUserIdentifier(), null, $keywords);
                        }
                    }
                    if (!empty($courses)) {
                        array_push($closest, $translator->trans('chatbot.found', ['%subject%' => $translator->trans('chatbot.keys.modules', [], null, $request->getLocale()), '%keywords%' => $keywords], null, $request->getLocale()));
                        $modules = [];
                        foreach ($courses as $course) if (!in_array($course->getModule()->getId(), $modules)) {
                            array_push(
                                $closest,
                                '<div>
                                    <h6 class="fw-bold fs-5 ">' . $course->getModule()->getLabel() . '</h6>
                                    <img src="' . $assets->getUrl('images/' . $course->getModule()->getIllustration()) . '" title="' . $course->getModule()->getIllustration() . '">
                                    <a class="btn btn-primary text-center" href="/' . $request->getLocale() . '/course/read/' . $course->getModule()->getUuid() . '">' . $translator->trans('global.btn_consult', [], null, $request->getLocale()) . '</a>
                                </div>'
                            );
                        }
                    } else {
                        $closest = [$translator->trans('chatbot.not_found', ['%keywords%' => $keywords], null, $request->getLocale())];
                    }
                    break;
                case strpos($userMessage, $translator->trans('chatbot.keys.users', [], null, $request->getLocale())) === 0:
                    $keywords = explode($translator->trans('chatbot.keys.users', [], null, $request->getLocale()) . ' ', trim($userMessage))[1];
                    $trainees = $traineeRepository->searchTrainees($keywords);
                    $trainers = $trainerRepository->searchTrainers($keywords);
                    if (!empty($trainees)) {
                        array_push($closest, $translator->trans('chatbot.found', ['%subject%' => $translator->trans('chatbot.keys.users', [], null, $request->getLocale()), '%keywords%' => $keywords], null, $request->getLocale()));
                        foreach ($trainees as $trainee) {
                            array_push(
                                $closest,
                                '<div>
                                    <div class="d-flex justify-content-center">
                                        <img src="' . $assets->getUrl('../avatars/' . $trainee->getAvatar()->getLink()) . '" width="100" height="100" title="' . $trainee->getAvatar()->getLabel() . '">
                                    </div>
                                    <h6 class="fw-bold fs-5 ">' . $trainee->getFirstName() . ' ' . $trainee->getLastName() . '</h6>
                                    <p class="fs-6">' . $trainee->getCohort()->getName() . '</p>
                                    
                                    <div class="d-flex justify-content-center">
                                        <a class="btn btn-primary text-center" href="/' . $request->getLocale() . '/mailbox/trainee/' . $trainee->getUuid() . '"><i class="fa-solid fa-paper-plane"></i></a>
                                        ' . ($this->isGranted('ROLE_TRAINER') ? '<a class="ms-2 btn btn-primary text-center" href="/' . $request->getLocale() . '/trainee/' . $trainee->getUuid() . '"><i class="fa-solid fa-eye"></i></a>' : '') . '
                                    </div>
                                </div>'
                            );
                        }
                    }

                    if (!empty($trainers)) {
                        array_push($closest, $translator->trans('chatbot.found', ['%subject%' => $translator->trans('chatbot.keys.users', [], null, $request->getLocale()), '%keywords%' => $keywords], null, $request->getLocale()));
                        foreach ($trainers as $trainer) {
                            array_push(
                                $closest,
                                '<div>
                                    <div class="d-flex justify-content-center">
                                        <img src="' . $assets->getUrl('../avatars/' . $trainer->getAvatar()->getLink()) . '" width="100" height="100" title="' . $trainer->getAvatar()->getLabel() . '">
                                    </div>
                                    <h6 class="fw-bold fs-5 ">[' . $trainer->getSector()->getLabel() . ']&nbsp;' . $trainer->getFirstName() . ' ' . $trainer->getLastName() . '</h6>
                                    <p class="fs-6">' . $trainer->getEmail() . '</p>
                                    
                                    <div class="d-flex justify-content-center">
                                        <a class="btn btn-primary text-center" href="/' . $request->getLocale() . '/mailbox/trainer/' . $trainer->getUuid() . '"><i class="fa-solid fa-paper-plane"></i></a>
                                        ' . ($this->isGranted('ROLE_TRAINER') ? '<a class="ms-2 btn btn-primary text-center" href="/' . $request->getLocale() . '/trainer/' . $trainer->getUuid() . '"><i class="fa-solid fa-eye"></i></a>' : '') . '
                                    </div>
                                </div>'
                            );
                        }
                    }

                    if (empty($trainees) && empty($trainers)) {
                        $closest = [$translator->trans('chatbot.not_found', ['%keywords%' => $keywords], null, $request->getLocale())];
                    }
                    break;
                case $translator->trans('chatbot.keys.help', [], null, $request->getLocale()):
                default:
                    $closest = ['<ul>
                                    <li><code>/' . $translator->trans('chatbot.keys.courses') . '&nbsp;{uuid|keywords}</code><p><small>' . $translator->trans('chatbot.commands.courses') . '</small></p></li>
                                    <li><code>/' . $translator->trans('chatbot.keys.modules') . '&nbsp;{uuid|keywords}</code><p><small>' . $translator->trans('chatbot.commands.modules') . '</small></p></li>
                                    <li><code>/' . $translator->trans('chatbot.keys.users') . '&nbsp;{uuid|username}</code><p><small>' . $translator->trans('chatbot.commands.users') . '</small></p></li>
                                </ul>'];
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

            // Solution Levenshtein ++
            // tableau de mots à vérifier

            // aucune distance de trouvée pour le moment
            $shortest = -1;

            // boucle sur les mots pour trouver le plus près
            foreach ($responses as $response) {

                // calcule la distance avec le mot mis en entrée,
                // et le mot courant
                $lev = levenshtein($userMessage, $response);

                // cherche une correspondance exacte
                if ($lev == 0) {

                    // le mot le plus près est celui-ci (correspondance exacte)
                    $closest = [$response];
                    $shortest = 0;

                    // on sort de la boucle ; nous avons trouvé une correspondance exacte
                    break;
                }

                // Si la distance est plus petite que la prochaine distance trouvée
                // OU, si le prochain mot le plus près n'a pas encore été trouvé
                if ($lev <= $shortest || $shortest < 0) {
                    // définition du mot le plus près ainsi que la distance
                    $closest  = [$response];
                    $shortest = $lev;
                }
            }
            // Fin solution Levenshtein ++
        }

        return $this->json(
            [
                'success' => true,
                'messages' => $closest,
            ],
            status: Response::HTTP_OK
        );
    }

    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    #[Route('/{_locale}/ai_chatbot/{message}', name: 'app_ai_chatbot', methods: ['GET'])]
    public function streamChat($message, TranslatorInterface $translator, Request $request): Response
    {
        $data = [
            'messages' => [
                ['role' => 'system', 'content' => $translator->trans('chatbot.ia.system', [], null, $request->getLocale())],
                ['role' => 'user', 'content' => urldecode($message)]
            ],
            'model' => 'llama3.1-8b-instruct',
            'stream' => true,
            'tool_choice' => 'none',
            'max_tokens' => 4096,
            'stop' => ['End'],
            'frequency_penalty' => 0.2,
            'presence_penalty' => 0.6,
            'temperature' => 0.35,
            'top_p' => 0.95,
            'modalities' => ['text'],
            'store' => true,
            'metadata' => [
                'type' => 'conversation'
            ],
            'logit_bias' => [],
            'logprobs' => null,
            'n' => 1,
            'response_format' => ['type' => 'text'],
            'seed' => rand(0, 99999),
            'stream_options' => null,
            'tools' => [
                [
                    'type' => 'function',
                    'function' => [
                        'name' => '',
                        'parameters' => [],
                        'strict' => null
                    ]
                ]
            ],
            'parallel_tool_calls' => null,
        ];
        $url = $this->getParameter("ai_url") . '/';
        if (!$this->isCurlServerAlive($url)) {
            $response = new StreamedResponse(function () use ($translator, $request) {
                ob_implicit_flush(true);
                header('Content-Type: text/event-stream');
                header('Cache-Control: no-cache');
                header("Access-Control-Allow-Origin: *");
                foreach (explode(' ', $translator->trans('chatbot.ia.not_available', [], null, $request->getLocale())) as $value) {
                    echo "data: " . $value . " \n\n";
                    ob_flush();
                    flush();
                }
                echo "data: [DONE]\n\n";
                ob_flush();
                flush();
            });
        } else {
            $response = new StreamedResponse(function () use ($data, $url) {
                ob_implicit_flush(true);
                header('Content-Type: text/event-stream');
                header('Cache-Control: no-cache');
                header("Access-Control-Allow-Origin: *");

                $url = $url . 'v1/chat/completions';

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($curl, $chunk) {
                    // file_put_contents("debug_raw.txt", $chunk . "\n", FILE_APPEND);

                    $cleanedData = preg_replace('/^data: /', '', trim($chunk));
                    $json = json_decode($cleanedData, true);
                    // file_put_contents("debug_log.txt", "Décodage JSON: " . print_r($json, true) . "\n", FILE_APPEND);

                    if (isset($json['choices'][0]['delta']['content'])) {
                        $cleanedContent = nl2br(htmlspecialchars_decode($json['choices'][0]['delta']['content'], ENT_QUOTES));
                        echo "data: " . $cleanedContent . "\n\n";
                        ob_flush();
                        flush();
                    }
                    return strlen($chunk);
                });

                curl_exec($ch);
                curl_close($ch);

                echo "data: [DONE]\n\n";
                ob_flush();
                flush();
            });
        }

        $response->headers->set('X-Accel-Buffering', 'no'); // Désactiver le buffering Nginx
        return $response;
    }

    /**
     * Permet de tester si le serveur est accessible
     * 
     * @param $url string URL du serveur à tester
     * @return bool true si le serveur est accessible, false sinon
     */
    function isCurlServerAlive($url): bool
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $result !== false && $httpCode === 200;
    }

    #[IsGranted(new Expression('is_granted("ROLE_TRAINEE")'))]
    #[Route('/send_agreement', name: 'app_send_agreement', methods: "POST")]
    public function sendAgreement(TranslatorInterface $translator, Request $request, EntityManagerInterface $entityManager, CohortInternshipRepository $cohortInternshipRepository, TraineeRepository $traineeRepository, TraineeInternshipRepository $traineeInternshipRepository): Response
    {
        if ($request->isXmlHttpRequest()) {
            $file = $request->files->get('file');
            $uuid = $request->request->get('uuid');

            if ($file) {
                if ($file->getSize() <= 52428800) {
                    if ($file->getMimeType() == 'application/pdf' || $file->getMimeType() == 'application/x-pdf') {
                        $trainee = $traineeRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
                        $nomFichier = "Convention_de_stage_" . strtoupper(str_replace(" ", "-", $trainee->getLastName())) . "_" . ucfirst(str_replace(" ", "-", $trainee->getFirstName())) . '.' . $file->guessExtension();
                        try {
                            $file->move($this->getParameter('internships_directory') . "/tmp", $nomFichier);
                            $this->addFlash('info', $translator->trans('internship.sent_agreement', [], null, $request->getLocale()));

                            $period = $cohortInternshipRepository->findOneBy(['uuid' => $uuid]);
                            $internship = $traineeInternshipRepository->findOneBy(['trainee' => $trainee, 'cohort_internship' => $period]);
                            if ($internship == null) {
                                $internship = new TraineeInternship();
                                $internship->setTrainee($trainee);
                                $internship->setAgreementLink($nomFichier);
                                $internship->setCohortInternship($period);
                            } else {
                                $internship->setAgreement(1);
                            }
                            $entityManager->persist($internship);

                            $entityManager->flush();
                        } catch (FileException $e) {
                            $this->addFlash('danger', $translator->trans('global.exceptions.other.content', [], null, $request->getLocale()) . " " . $e->getMessage());
                        }
                        return $this->json(
                            [
                                'success' => true,
                            ],
                            status: Response::HTTP_OK
                        );
                    }
                    $this->addFlash('danger', $translator->trans('global.exceptions.format.content', ['%formats%' => 'PDF'], null, $request->getLocale()));
                    return $this->json(
                        [
                            'success' => false,
                        ],
                        status: Response::HTTP_BAD_REQUEST
                    );
                }
                $this->addFlash('danger', $translator->trans('global.exceptions.size.content', ['%size%' => '50', '%currentSize%' => $file->getSize()], null, $request->getLocale()));
                return $this->json(
                    [
                        'success' => false,
                    ],
                    status: Response::HTTP_BAD_REQUEST
                );
            }
            $this->addFlash('danger', $translator->trans('global.exceptions.other.content', [], null, $request->getLocale()));
            return $this->json(
                [
                    'success' => false,
                ],
                status: Response::HTTP_BAD_REQUEST
            );
        }
        $this->addFlash('danger', $translator->trans('global.exceptions.other.content', [], null, $request->getLocale()));
        return $this->json(
            [
                'success' => false,
            ],
            status: Response::HTTP_NOT_FOUND
        );
    }
    #[IsGranted(new Expression('is_granted("ROLE_TRAINEE")'))]
    #[Route('/send_certificate', name: 'app_send_certificate', methods: "POST")]
    public function sendCertificate(TranslatorInterface $translator, Request $request, EntityManagerInterface $entityManager, TraineeRepository $traineeRepository, CohortInternshipRepository $cohortInternshipRepository, TraineeInternshipRepository $traineeInternshipRepository): Response
    {
        if ($request->isXmlHttpRequest()) {
            $file = $request->files->get('file');
            $uuid = $request->request->get('uuid');

            if ($file) {
                if ($file->getSize() <= 52428800) {
                    if ($file->getMimeType() == 'application/pdf' || $file->getMimeType() == 'application/x-pdf') {
                        $trainee = $traineeRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
                        $nomFichier = "Attestation_de_stage_" . strtoupper(str_replace(" ", "-", $trainee->getLastName())) . "_" . ucfirst(str_replace(" ", "-", $trainee->getFirstName())) . '.' . $file->guessExtension();
                        try {
                            $file->move($this->getParameter('internships_directory') . "/tmp", $nomFichier);
                            $this->addFlash('info', $translator->trans('internship.sent_certificate', [], null, $request->getLocale()));

                            $period = $cohortInternshipRepository->findOneBy(['uuid' => $uuid]);
                            $internship = $traineeInternshipRepository->findOneBy(['trainee' => $trainee, 'cohort_internship' => $period]);
                            $internship->setCertificate(0);
                            $entityManager->persist($internship);

                            $entityManager->flush();
                        } catch (FileException $e) {
                            $this->addFlash('danger', $translator->trans('global.exceptions.other.content', [], null, $request->getLocale()) . " " . $e->getMessage());
                        }
                        return $this->json(
                            [
                                'success' => true,
                            ],
                            status: Response::HTTP_OK
                        );
                    }
                    $this->addFlash('danger', $translator->trans('global.exceptions.format.content', ['%formats%' => 'PDF'], null, $request->getLocale()));
                    return $this->json(
                        [
                            'success' => false,
                        ],
                        status: Response::HTTP_BAD_REQUEST
                    );
                }
                $this->addFlash('danger', $translator->trans('global.exceptions.size.content', ['%size%' => '50', '%currentSize%' => $file->getSize()], null, $request->getLocale()));
                return $this->json(
                    [
                        'success' => false,
                    ],
                    status: Response::HTTP_BAD_REQUEST
                );
            }
            $this->addFlash('danger', $translator->trans('global.exceptions.other.content', [], null, $request->getLocale()));
            return $this->json(
                [
                    'success' => false,
                ],
                status: Response::HTTP_BAD_REQUEST
            );
        }
        $this->addFlash('danger', $translator->trans('global.exceptions.other.content', [], null, $request->getLocale()));
        return $this->json(
            [
                'success' => false,
            ],
            status: Response::HTTP_NOT_FOUND
        );
    }

    #[IsGranted(new Expression('is_granted("ROLE_TRAINEE")'))]
    #[Route('/send_evaluation', name: 'app_send_evaluation', methods: "POST")]
    public function sendEvaluation(TranslatorInterface $translator, Request $request, EntityManagerInterface $entityManager, TraineeRepository $traineeRepository, CohortInternshipRepository $cohortInternshipRepository, TraineeInternshipRepository $traineeInternshipRepository): Response
    {
        if ($request->isXmlHttpRequest()) {
            $file = $request->files->get('file');
            $uuid = $request->request->get('uuid');

            if ($file) {
                if ($file->getSize() <= 52428800) {
                    if ($file->getMimeType() == 'application/pdf' || $file->getMimeType() == 'application/x-pdf') {
                        $trainee = $traineeRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
                        $nomFichier = "Evaluation_de_stage_" . strtoupper(str_replace(" ", "-", $trainee->getLastName())) . "_" . ucfirst(str_replace(" ", "-", $trainee->getFirstName())) . '.' . $file->guessExtension();
                        try {
                            $file->move($this->getParameter('internships_directory') . "/tmp", $nomFichier);
                            $this->addFlash('info', $translator->trans('internship.sent_evaluation', [], null, $request->getLocale()));

                            $period = $cohortInternshipRepository->findOneBy(['uuid' => $uuid]);
                            $internship = $traineeInternshipRepository->findOneBy(['trainee' => $trainee, 'cohort_internship' => $period]);
                            $internship->setEvaluation(0);
                            $entityManager->persist($trainee);

                            $entityManager->flush();
                        } catch (FileException $e) {
                            $this->addFlash('danger', $translator->trans('global.exceptions.other.content', [], null, $request->getLocale()) . " " . $e->getMessage());
                        }
                        return $this->json(
                            [
                                'success' => true,
                            ],
                            status: Response::HTTP_OK
                        );
                    }
                    $this->addFlash('danger', $translator->trans('global.exceptions.format.content', ['%formats%' => 'PDF'], null, $request->getLocale()));
                    return $this->json(
                        [
                            'success' => false,
                        ],
                        status: Response::HTTP_BAD_REQUEST
                    );
                }
                $this->addFlash('danger', $translator->trans('global.exceptions.size.content', ['%size%' => '50', '%currentSize%' => $file->getSize()], null, $request->getLocale()));
                return $this->json(
                    [
                        'success' => false,
                    ],
                    status: Response::HTTP_BAD_REQUEST
                );
            }
            $this->addFlash('danger', $translator->trans('global.exceptions.other.content', [], null, $request->getLocale()));
            return $this->json(
                [
                    'success' => false,
                ],
                status: Response::HTTP_BAD_REQUEST
            );
        }
        $this->addFlash('danger', $translator->trans('global.exceptions.other.content', [], null, $request->getLocale()));
        return $this->json(
            [
                'success' => false,
            ],
            status: Response::HTTP_NOT_FOUND
        );
    }

    #[IsGranted(new Expression('is_granted("ROLE_TRAINER") or is_granted("ROLE_TRAINEE")'))]
    #[Route('/send_message', name: 'app_send_message', methods: "POST")]
    public function sendMessage(TranslatorInterface $translator, Request $request, UserRepository $userRepository, CohortRepository $cohortRepository, MessageRepository $messageRepository, EntityManagerInterface $entityManager): Response
    {
        $notificationNewMessage = new Notification();
        $notificationNewMessage->setDate(new DateTimeImmutable());
        $notificationNewMessage->setCategory("new_message");
        $notificationNewMessage->setOrigin($this->getUser()->getUserIdentifier());
        $notificationNewMessage->setMessage($this->getUser()->getUserIdentifier());

        $message = new Message();
        $message->setContent($request->request->get('form_message'));
        $message->setDate(new DateTimeImmutable());
        $message->setDocument(null);
        $message->setMimeType(null);
        if ($request->request->get('form_original_message') !== null && !empty($request->request->get('form_original_message'))) {
            $message->setOriginalMessage($messageRepository->find($request->request->get('form_original_message')));
        } else {
            $message->setOriginalMessage(null);
        }

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
                $notificationNewMessage->setDate(new DateTimeImmutable());
                $notificationNewMessage->setCategory("new_message");
                $notificationNewMessage->setOrigin($this->getUser()->getUserIdentifier());
                $notificationNewMessage->setUser($cohortTrainee);
                $notificationNewMessage->setMessage($cohort->getName());
                $notificationNewMessage->setLink($this->generateUrl('app_mailbox_cohort', ['uuid' => $cohort->getUuid()]), true);
                $entityManager->persist($notificationNewMessage);
                $entityManager->flush();
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

        if (!empty($_FILES['form_file'])) {
            $file = $_FILES['form_file'];
            $file['name'] = uniqid('upload_') . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
            if (move_uploaded_file($file['tmp_name'], $this->getParameter('messages_directory') . '/' . $file['name'])) {
                $message->setDocument($file['name']);
                $message->setMimeType($file['type']);
            } else {
                $this->addFlash('danger', $translator->trans('global.exceptions.other.content', [], null, $request->getLocale()));
            }
        }
        $entityManager->persist($message);
        $entityManager->flush();
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
    public function sendTp(TranslatorInterface $translator, Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager, CourseResourceRepository $courseResourceRepository, NotificationRepository $notificationRepository, UserRepository $userRepository): Response
    {
        if ($request->isXmlHttpRequest()) {
            $file = $request->files->get('file');

            if ($file) {
                if ($file->getSize() <= 52428800) {
                    if (
                        $file->getMimeType() == 'application/pdf' ||
                        $file->getMimeType() == 'application/x-pdf' ||
                        $file->getMimeType() == 'application/x-rar-compressed' ||
                        $file->getMimeType() == 'application/x-tar' ||
                        $file->getMimeType() == 'application/zip' ||
                        $file->getMimeType() == 'application/x-zip-compressed' ||
                        $file->getMimeType() == 'application/x-7z-compressed'
                    ) {
                        $nomFichier = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $nomFichier = $slugger->slug($nomFichier);
                        $nomFichier = $nomFichier . '-' . uniqid() . '.' . $file->guessExtension();
                        try {
                            $file->move($this->getParameter('homeworks_directory') . '/' . $this->getUser()->getUserIdentifier(), $nomFichier);
                            $traineeResource = new TraineeResource();
                            $traineeResource->setLabel($nomFichier);
                            $courseResource = $courseResourceRepository->find($request->request->get('tp_id'));
                            $traineeResource->setCourseResource($courseResource);
                            $traineeResource->setTrainee($this->getUser());
                            $entityManager->persist($traineeResource);
                            $entityManager->flush();

                            $currentUser = $userRepository->findOneBy(["username" => $this->getUser()->getUserIdentifier()]);
                            $notificationRepository->deleteANotification($courseResource->getCourse()->getModule()->getLabel(), null, "homework_to_do", $currentUser->getId());

                            $this->addFlash('notice', $translator->trans('global.file_sent', [], null, $request->getLocale()));
                            return $this->json(
                                [
                                    'success' => true,
                                ],
                                status: Response::HTTP_OK
                            );
                        } catch (FileException $e) {
                            $this->addFlash('danger', $translator->trans('global.exceptions.other.content', [], null, $request->getLocale()) . $e->getMessage());
                        }
                        return $this->json(
                            [
                                'success' => false,
                            ],
                            status: Response::HTTP_BAD_REQUEST
                        );
                    }
                    $this->addFlash('danger', $translator->trans('global.exceptions.format.content', ['%formats%' => 'PDF'], null, $request->getLocale()));
                    return $this->json(
                        [
                            'success' => false,
                        ],
                        status: Response::HTTP_BAD_REQUEST
                    );
                }
                $this->addFlash('danger', $translator->trans('global.exceptions.size.content', ['%size%' => '50', '%currentSize%' => $file->getSize()], null, $request->getLocale()));
                return $this->json(
                    [
                        'success' => false,
                    ],
                    status: Response::HTTP_BAD_REQUEST
                );
            }
            $this->addFlash('danger', $translator->trans('global.exceptions.other.content', [], null, $request->getLocale()));
            return $this->json(
                [
                    'success' => false,
                ],
                status: Response::HTTP_BAD_REQUEST
            );
        }
        $this->addFlash('danger', $translator->trans('global.exceptions.other.content', [], null, $request->getLocale()));
        return $this->json(
            [
                'success' => false,
            ],
            status: Response::HTTP_NOT_FOUND
        );
    }

    #[IsGranted(new Expression('is_granted("ROLE_TRAINEE")'))]
    #[Route('/save_course_to_favorites/{course}', name: 'app_add_course_favorite', methods: "POST")]
    public function saveCourseToFavorites(TranslatorInterface $translator, Request $request, EntityManagerInterface $entityManager, TraineeRepository $traineeRepository, CourseRepository $courseRepository, string $course): Response
    {
        if ($request->isXmlHttpRequest()) {
            $course = $courseRepository->findOneBy(['link' => $course]);
            if ($course instanceof Course) {
                $favorite = new TraineeCourseFavorite();
                $favorite->setCourse($course);
                $favorite->setTrainee($traineeRepository->findOneBy(["username" => $this->getUser()->getUserIdentifier()]));
                $entityManager->persist($favorite);
                $entityManager->flush();
                $this->addFlash('notice', $translator->trans('course.action.favorite.added', [], null, $request->getLocale()));
                return $this->json(
                    [
                        'success' => true,
                    ],
                    status: Response::HTTP_OK
                );
            }
            $this->addFlash('danger', $translator->trans('global.exceptions.404.content', [], null, $request->getLocale()));
            return $this->json(
                [
                    'success' => false,
                ],
                status: Response::HTTP_BAD_REQUEST
            );
        }
        $this->addFlash('danger', $translator->trans('global.exceptions.other.content', [], null, $request->getLocale()));
        return $this->json(
            [
                'success' => false,
            ],
            status: Response::HTTP_NOT_FOUND
        );
    }

    #[IsGranted(new Expression('is_granted("ROLE_TRAINEE")'))]
    #[Route('/remove_course_from_favorites/{course}', name: 'app_remove_course_favorite', methods: "POST")]
    public function removeCourseFromFavorites(TranslatorInterface $translator, Request $request, EntityManagerInterface $entityManager, TraineeCourseFavoriteRepository $traineeCourseFavoriteRepository, TraineeRepository $traineeRepository, CourseRepository $courseRepository, string $course): Response
    {
        if ($request->isXmlHttpRequest()) {
            $course = $courseRepository->findOneBy(['link' => $course]);
            if ($course instanceof Course) {
                $favorite = $traineeCourseFavoriteRepository->findOneBy(["course" => $course, "trainee" => $traineeRepository->findOneBy(["username" => $this->getUser()->getUserIdentifier()])]);
                $entityManager->remove($favorite);
                $entityManager->flush();
                $this->addFlash('notice', $translator->trans('course.action.favorite.deleted', [], null, $request->getLocale()));
                return $this->json(
                    [
                        'success' => true,
                    ],
                    status: Response::HTTP_OK
                );
            }
            $this->addFlash('danger', $translator->trans('global.exceptions.404.content', [], null, $request->getLocale()));
            return $this->json(
                [
                    'success' => false,
                ],
                status: Response::HTTP_BAD_REQUEST
            );
        }
        $this->addFlash('danger', $translator->trans('global.exceptions.other.content', [], null, $request->getLocale()));
        return $this->json(
            [
                'success' => false,
            ],
            status: Response::HTTP_NOT_FOUND
        );
    }

    #[IsGranted(new Expression('is_granted("ROLE_TRAINEE")'))]
    #[Route('/save_completed_tutorial', name: 'app_save_completed_tutorial', methods: "POST")]
    public function saveCompletedTutorial(TranslatorInterface $translator, Request $request, EntityManagerInterface $entityManager, TraineeRepository $traineeRepository): Response
    {
        if ($request->isXmlHttpRequest()) {
            $tutorials = $request->request->get('tours');
            $trainee = $traineeRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
            $trainee->setTutorialCompleted($tutorials);
            $entityManager->persist($trainee);
            $entityManager->flush();
            $this->addFlash('info', $translator->trans('tourguide.mark_as_done', [], null, $request->getLocale()));
            return $this->json(
                [
                    'success' => true,
                ],
                status: Response::HTTP_OK
            );
        }
        $this->addFlash('danger', $translator->trans('global.exceptions.400.content', [], null, $request->getLocale()));
        return $this->json(
            [
                'success' => false,
            ],
            status: Response::HTTP_BAD_REQUEST
        );
    }
}
