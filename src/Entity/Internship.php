<?php

namespace App\Entity;

use App\Repository\InternshipRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: InternshipRepository::class)]
#[ORM\UniqueConstraint(name: "unique_trainee_prospect", columns: ["trainee_id", "prospect_id"])]
#[Broadcast]
class Internship
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'internships')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trainee $trainee = null;

    #[ORM\ManyToOne(inversedBy: 'internships')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Prospect $prospect = null;

    #[ORM\Column(length: 75)]
    private ?string $tutorLastName = null;

    #[ORM\Column(length: 75)]
    private ?string $tutorFirstName = null;

    #[ORM\Column(length: 255)]
    private ?string $tutorEmail = null;

    #[ORM\Column(length: 10)]
    private ?string $tutorPhoneNumber = null;

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

    public function getProspect(): ?Prospect
    {
        return $this->prospect;
    }

    public function setProspect(?Prospect $prospect): static
    {
        $this->prospect = $prospect;

        return $this;
    }

    public function getTutorLastName(): ?string
    {
        return $this->tutorLastName;
    }

    public function setTutorLastName(string $tutorLastName): static
    {
        $this->tutorLastName = $tutorLastName;

        return $this;
    }

    public function getTutorFirstName(): ?string
    {
        return $this->tutorFirstName;
    }

    public function setTutorFirstName(string $tutorFirstName): static
    {
        $this->tutorFirstName = $tutorFirstName;

        return $this;
    }

    public function getTutorEmail(): ?string
    {
        return $this->tutorEmail;
    }

    public function setTutorEmail(string $tutorEmail): static
    {
        $this->tutorEmail = $tutorEmail;

        return $this;
    }

    public function getTutorPhoneNumber(): ?string
    {
        return $this->tutorPhoneNumber;
    }

    public function setTutorPhoneNumber(string $tutorPhoneNumber): static
    {
        $this->tutorPhoneNumber = $tutorPhoneNumber;

        return $this;
    }
}
