<?php

namespace App\Entity;

use App\Repository\TraineeResourceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: TraineeResourceRepository::class)]
#[ORM\UniqueConstraint(name: "unique_trainee_resource", columns: ["trainee_id", "course_resource_id"])]
#[Broadcast]
class TraineeResource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userResources')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $trainee = null;

    #[ORM\ManyToOne(inversedBy: 'traineeResources')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CourseResource $courseResource = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrainee(): ?User
    {
        return $this->trainee;
    }

    public function setTrainee(?User $trainee): static
    {
        $this->trainee = $trainee;

        return $this;
    }

    public function getCourseResource(): ?CourseResource
    {
        return $this->courseResource;
    }

    public function setCourseResource(?CourseResource $resource): static
    {
        $this->courseResource = $resource;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }
}
