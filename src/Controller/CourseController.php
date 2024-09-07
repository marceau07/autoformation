<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\CourseCohort;
use App\Entity\Notification;
use App\Form\CourseType;
use App\Repository\CohortRepository;
use App\Repository\CourseCohortRepository;
use App\Repository\CourseRepository;
use App\Repository\NotificationRepository;
use App\Repository\TrainerRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted('ROLE_TRAINER')]
#[Route('/{_locale}/course')]
class CourseController extends AbstractController
{
    #[Route('/', name: 'app_course_index', methods: ['GET'])]
    public function index(CourseRepository $courseRepository, TrainerRepository $trainerRepository, UserRepository $userRepository, CourseCohortRepository $courseCohortRepository): Response
    {
        $trainer = $trainerRepository->find($userRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()])->getId());

        return $this->render('course/index.html.twig', [
            'courses' => $courseRepository->findAll(),
            'trainerCohorts' => $trainer->getCohorts(),
            'courseCohorts' => $courseCohortRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_course_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $course = new Course();
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($course);
            $entityManager->flush();

            return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('course/new.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_course_show', methods: ['GET'])]
    public function show(Course $course): Response
    {
        return $this->render('course/show.html.twig', [
            'course' => $course,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_course_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('course/edit.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_course_delete', methods: ['POST'])]
    public function delete(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $course->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($course);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/cohort/fetch/{cohortId}/active/{active}/search/{search}', requirements: ['search' => '\w*'], name: 'app_courses_fetch', methods: ['POST'])]
    public function fetch(Request $request, CourseCohortRepository $courseCohortRepository, TranslatorInterface $translator, CourseRepository $courseRepository, CohortRepository $cohortRepository, int $cohortId, bool $active, string $search): Response
    {
        $cohort = $cohortRepository->find($cohortId);
        $courses = $courseRepository->getCoursesInformationsBySector(null, $search);
        $html = '<div class="row">';
        $html_bis = '';
        if (!empty($courses)) {
            foreach ($courses as $course) {
                $isActive = $courseCohortRepository->findOneBy(['course' => $course->getId(), 'cohort' => $cohortId]) ? $courseCohortRepository->findOneBy(['course' => $course->getId(), 'cohort' => $cohortId])->isActive() : false;
                if ($active) {
                    $html_bis .= $this->renderView('_components/_card4.html.twig', [
                        'course' => $course,
                        'cohort' => $cohort,
                        'active' => $active,
                        'isActive' => $isActive,
                    ]);
                } else if (!$isActive) {
                    $html_bis .= $this->renderView('_components/_card4.html.twig', [
                        'course' => $course,
                        'cohort' => $cohort,
                        'active' => $active,
                        'isActive' => $isActive,
                    ]);
                }
            }
            if ($html_bis === '') {
                $html .= $translator->trans('course.filtered.no_content', [], null, $request->getLocale());
            }
        } else {
            $html = $translator->trans('course.no_content', [], null, $request->getLocale());
        }
        $html .= $html_bis;
        $html .= '</div>';

        return new JsonResponse($html);
    }

    #[Route('/cohort/{courseId}/{cohortId}/update', name: 'app_course_cohort_update', methods: ['POST', 'GET'])]
    public function updateCourseCohort(EntityManagerInterface $entityManager, UserRepository $userRepository, NotificationRepository $notificationRepository, CourseCohortRepository $courseCohortRepository, CohortRepository $cohortRepository, CourseRepository $courseRepository, int $courseId, int $cohortId): Response
    {
        $course = $courseRepository->find($courseId);
        $cohort = $cohortRepository->find($cohortId);

        $courseCohort = $courseCohortRepository->findOneBy(['course' => $course, 'cohort' => $cohort]);
        $currentUser = $userRepository->findOneBy(["username" => $this->getUser()->getUserIdentifier()]);
        if ($courseCohort) {
            if ($courseCohort->isActive()) { // If notification exists, delete it to avoid accessing it again
                foreach ($cohort->getTrainees() as $trainee) {
                    $notificationRepository->deleteANotification("[" . $course->getModule()->getLabel() . "]", null, "new_course", $trainee->getId());
                }
            } else {
                foreach ($cohort->getTrainees() as $trainee) {
                    $notification = new Notification();
                    $notification->setOrigin('[' . $course->getModule()->getLabel() . ']');
                    $notification->setMessage($course->getTitle());
                    $notification->setLink('/embed/' . $course->getLink());
                    $notification->setDate(new DateTimeImmutable());
                    $notification->setCategory('new_course');
                    $notification->setUser($trainee);
                    $entityManager->persist($notification);
                }
            }
            $courseCohort->setActive(!$courseCohort->isActive());
        } else {
            $courseCohort = new CourseCohort();
            $courseCohort->setCourse($course);
            $courseCohort->setCohort($cohort);
            $courseCohort->setActive(true);

            foreach ($cohort->getTrainees() as $trainee) {
                $notification = new Notification();
                $notification->setOrigin('[' . $course->getModule()->getLabel() . ']');
                $notification->setMessage($course->getTitle());
                $notification->setLink('/embed/' . $course->getLink());
                $notification->setDate(new DateTimeImmutable());
                $notification->setCategory('new_course');
                $notification->setUser($trainee);
                $entityManager->persist($notification);
            }
        }
        if (!empty($course->getCourseResources())) {
            foreach ($course->getCourseResources() as $resource) {
                if ($resource->getType() === "tp") {
                    foreach ($cohort->getTrainees() as $trainee) {
                        $notification = new Notification();
                        $notification->setOrigin('[' . $course->getModule()->getLabel() . ']');
                        $notification->setMessage($resource->getTitle());
                        $notification->setLink('/embed/' . $course->getLink() . '/#tp_' . $course->getId() . '_' . $resource->getId());
                        $notification->setDate(new DateTimeImmutable());
                        $notification->setCategory('homework_to_do');
                        $notification->setUser($trainee);
                        $entityManager->persist($notification);
                    }
                }
            }
        }


        $entityManager->persist($courseCohort);
        $entityManager->flush();
        $this->addFlash('success', '[' . $course->getTitle() . ']:[' . $cohort->getName() . '] updated successfully to ' . ($courseCohort->isActive() ? 'visible' : 'invisible') . '!'); // TODO: check why not working
        return $this->redirectToRoute('app_course_index', []);
    }
}
