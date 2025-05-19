<?php

namespace App\Entity;

use App\Repository\InternshipRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Cache(usage: "NONSTRICT_READ_WRITE", region: "non_strict")]
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

    /**
     * @var Collection<int, TraineeInternship>
     */
    #[ORM\OneToMany(targetEntity: TraineeInternship::class, mappedBy: 'internship')]
    private Collection $traineeInternships;

    public function __construct()
    {
        $this->traineeInternships = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, TraineeInternship>
     */
    public function getTraineeInternships(): Collection
    {
        return $this->traineeInternships;
    }

    public function addTraineeInternship(TraineeInternship $traineeInternship): static
    {
        if (!$this->traineeInternships->contains($traineeInternship)) {
            $this->traineeInternships->add($traineeInternship);
            $traineeInternship->setInternship($this);
        }

        return $this;
    }

    public function removeTraineeInternship(TraineeInternship $traineeInternship): static
    {
        if ($this->traineeInternships->removeElement($traineeInternship)) {
            // set the owning side to null (unless already changed)
            if ($traineeInternship->getInternship() === $this) {
                $traineeInternship->setInternship(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return '(' . $this->getProspect()->getSiren() . $this->getProspect()->getNic() . ') ' . $this->getProspect()->getName() . ' - ' . $this->getTutorLastName() . ' ' . $this->getTutorFirstName();
    }
}
