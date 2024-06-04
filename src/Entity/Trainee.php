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
    private ?array $documents = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $diploma = null;

    #[ORM\OneToMany(targetEntity: CourseTrainee::class, mappedBy: 'trainee')]
    private Collection $courseTrainees;

    #[ORM\ManyToOne(inversedBy: 'trainees')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Session $session = null;

    #[ORM\OneToMany(targetEntity: SurveyTrainee::class, mappedBy: 'trainee')]
    private Collection $surveyTrainees;

    public function __construct()
    {
        $roles = $this->getRoles();
        $roles[] = "ROLE_TRAINEE";
        $this->setRoles($roles);
        $this->courseTrainees = new ArrayCollection();
        $this->surveyTrainees = new ArrayCollection();
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

    public function getDocuments(): ?array
    {
        return $this->documents;
    }

    public function setDocuments(?array $documents): static
    {
        $this->documents = $documents;

        return $this;
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

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): static
    {
        $this->session = $session;

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
}
