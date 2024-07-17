<?php

namespace App\Service;

use App\Entity\FeedbackCategory;
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
}
