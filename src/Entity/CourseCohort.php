<?php

namespace App\Entity;

use App\Repository\CourseCohortRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: CourseCohortRepository::class)]
#[ORM\UniqueConstraint(name: "unique_course_cohort", columns: ["course_id", "cohort_id"])]
#[Broadcast]
class CourseCohort
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'courseCohorts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cohort $cohort = null;

    #[ORM\ManyToOne(inversedBy: 'courseCohorts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Course $course = null;

    #[ORM\Column]
    private ?bool $active = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCohort(): ?Cohort
    {
        return $this->cohort;
    }

    public function setCohort(?Cohort $cohort): static
    {
        $this->cohort = $cohort;

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

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }
}
