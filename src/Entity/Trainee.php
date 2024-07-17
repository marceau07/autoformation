<?php

namespace App\Entity;

use App\Repository\TraineeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: TraineeRepository::class)]
#[Broadcast]
class Trainee extends User implements UserInterface
{
    #[ORM\Column(length: 60)]
    private ?string $passwordSave = null;

    #[ORM\Column(nullable: true)]
    private ?string $documents = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $diploma = null;

    #[ORM\OneToMany(targetEntity: CourseTrainee::class, mappedBy: 'trainee')]
    private Collection $courseTrainees;

    #[ORM\ManyToOne(inversedBy: 'trainees')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cohort $cohort = null;

    #[ORM\OneToMany(targetEntity: SurveyTrainee::class, mappedBy: 'trainee')]
    private Collection $surveyTrainees;

    /**
     * @var Collection<int, TraineeResource>
     */
    #[ORM\OneToMany(targetEntity: TraineeResource::class, mappedBy: 'trainee')]
    private Collection $traineeResources;

    /**
     * @var Collection<int, Internship>
     */
    #[ORM\OneToMany(targetEntity: Internship::class, mappedBy: 'trainee')]
    private Collection $internships;

    public function __construct()
    {
        $roles = $this->getRoles();
        $roles[] = "ROLE_TRAINEE";
        $this->setRoles($roles);
        $this->courseTrainees = new ArrayCollection();
        $this->surveyTrainees = new ArrayCollection();
        $this->traineeResources = new ArrayCollection();
        $this->internships = new ArrayCollection();
    }

    public function getPasswordSave(): ?string
    {
        return $this->passwordSave;
    }

    public function setPasswordSave(string $passwordSave): static
    {
        $this->passwordSave = $passwordSave;

        return $this;
    }

    public function getDocuments(): ?string
    {
        return $this->documents;
    }

    public function setDocuments(?string $documents): static
    {
        $this->documents = $documents;

        return $this;
    }

    public function getDiplomaLabel(): string
    {
        switch($this->diploma) {
            case 0:
                return "Pas de diplôme";
            case -1:
                return "Diplômé·e partiellement (CCP1)";
            case -2:
                return "Diplômé·e partiellement (CCP2)";
            case -3:
                return "Diplômé·e partiellement (CCP3)";
            case -4:
                return "Ne s'est pas présenté·e à l'examen";
            case 1:
                return "Diplômé·e";
            default:
                return "Non renseigné";
        }
    }

    public function getDiploma(): ?int
    {
        return $this->diploma;
    }

    public function setDiploma(?int $diploma): static
    {
        $this->diploma = $diploma;

        return $this;
    }

    /**
     * @return Collection<int, CourseTrainee>
     */
    public function getCourseTrainees(): Collection
    {
        return $this->courseTrainees;
    }

    public function addCourseTrainee(CourseTrainee $courseTrainee): static
    {
        if (!$this->courseTrainees->contains($courseTrainee)) {
            $this->courseTrainees->add($courseTrainee);
            $courseTrainee->setTrainee($this);
        }

        return $this;
    }

    public function removeCourseTrainee(CourseTrainee $courseTrainee): static
    {
        if ($this->courseTrainees->removeElement($courseTrainee)) {
            // set the owning side to null (unless already changed)
            if ($courseTrainee->getTrainee() === $this) {
                $courseTrainee->setTrainee(null);
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

    /**
     * @return Collection<int, SurveyTrainee>
     */
    public function getSurveyTrainees(): Collection
    {
        return $this->surveyTrainees;
    }

    public function addSurveyTrainee(SurveyTrainee $surveyTrainee): static
    {
        if (!$this->surveyTrainees->contains($surveyTrainee)) {
            $this->surveyTrainees->add($surveyTrainee);
            $surveyTrainee->setTrainee($this);
        }

        return $this;
    }

    public function removeSurveyTrainee(SurveyTrainee $surveyTrainee): static
    {
        if ($this->surveyTrainees->removeElement($surveyTrainee)) {
            // set the owning side to null (unless already changed)
            if ($surveyTrainee->getTrainee() === $this) {
                $surveyTrainee->setTrainee(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TraineeResource>
     */
    public function getTraineeResources(): Collection
    {
        return $this->traineeResources;
    }

    public function addTraineeResource(TraineeResource $traineeResource): static
    {
        if (!$this->traineeResources->contains($traineeResource)) {
            $this->traineeResources->add($traineeResource);
            $traineeResource->setTrainee($this);
        }

        return $this;
    }

    public function removeTraineeResource(TraineeResource $traineeResource): static
    {
        if ($this->traineeResources->removeElement($traineeResource)) {
            // set the owning side to null (unless already changed)
            if ($traineeResource->getTrainee() === $this) {
                $traineeResource->setTrainee(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Internship>
     */
    public function getInternships(): Collection
    {
        return $this->internships;
    }

    public function addInternship(Internship $internship): static
    {
        if (!$this->internships->contains($internship)) {
            $this->internships->add($internship);
            $internship->setTrainee($this);
        }

        return $this;
    }

    public function removeInternship(Internship $internship): static
    {
        if ($this->internships->removeElement($internship)) {
            // set the owning side to null (unless already changed)
            if ($internship->getTrainee() === $this) {
                $internship->setTrainee(null);
            }
        }

        return $this;
    }
}
