<?php

namespace App\Entity;

use App\Repository\TraineeCourseFavoriteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: TraineeCourseFavoriteRepository::class)]
#[ORM\UniqueConstraint(name: "unique_trainee_course", columns: ["trainee_id", "course_id"])]
#[Broadcast]
class TraineeCourseFavorite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'traineeCourseFavorites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trainee $trainee = null;

    #[ORM\ManyToOne(inversedBy: 'traineeCourseFavorites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Course $course = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrainee(): ?Trainee
    {
        return $this->trainee;
    }

    public function setTrainee(?Trainee $trainee): static
    {
        $this->trainee = $trainee;

        return $this;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): static
    {
        $this->course = $course;

        return $this;
    }
}
