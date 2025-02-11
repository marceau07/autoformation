<?php

namespace App\Entity;

use App\Repository\CohortInternshipRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CohortInternshipRepository::class)]
class CohortInternship
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 75)]
    private ?string $label = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $start_date = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $finish_date = null;

    #[ORM\Column]
    private ?int $duration = null;

    /**
     * @var Collection<int, TraineeInternship>
     */
    #[ORM\OneToMany(targetEntity: TraineeInternship::class, mappedBy: 'cohort_internship')]
    private Collection $traineeInternships;

    #[ORM\ManyToOne(inversedBy: 'cohortInternships')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cohort $cohort = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $uuid = null;

    public function __construct()
    {
        $this->traineeInternships = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTimeImmutable $start_date): static
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getFinishDate(): ?\DateTimeImmutable
    {
        return $this->finish_date;
    }

    public function setFinishDate(\DateTimeImmutable $finish_date): static
    {
        $this->finish_date = $finish_date;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

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
            $traineeInternship->setCohortInternship($this);
        }

        return $this;
    }

    public function removeTraineeInternship(TraineeInternship $traineeInternship): static
    {
        if ($this->traineeInternships->removeElement($traineeInternship)) {
            // set the owning side to null (unless already changed)
            if ($traineeInternship->getCohortInternship() === $this) {
                $traineeInternship->setCohortInternship(null);
            }
        }

        return $this;
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

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }
}
