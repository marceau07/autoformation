<?php

namespace App\Service;

use App\Entity\FeedbackCategory;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;

class GlobalDataService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getFeedbackCategories()
    {
        // Replace YourEntity with the actual entity you want to fetch data from
        return $this->entityManager->getRepository(FeedbackCategory::class)->findAll();
    }

    public function getNotificationsHomeworksToDo($user)
    {
        // return $this->entityManager->getRepository(CourseResource::class)->findAll();
        return $this->entityManager->getRepository(Notification::class)->findBy(['category' => 'homework_to_do', 'user' => $user], ['date' => 'DESC']);
    }

    public function getNotificationsMessages($user)
    {
        // return $this->entityManager->getRepository(Message::class)->findAll();
        return $this->entityManager->getRepository(Notification::class)->findBy(['category' => 'new_message', 'user' => $user], ['date' => 'DESC']);
    }

    public function getNotificationsInternships($user)
    {
        return $this->entityManager->getRepository(Notification::class)->findBy(['category' => 'new_internship', 'user' => $user], ['date' => 'DESC']);
    }

    public function getNotificationsNewCourses($user)
    {
        return $this->entityManager->getRepository(Notification::class)->findBy(['category' => 'new_course', 'user' => $user], ['date' => 'DESC']);
    }
}
