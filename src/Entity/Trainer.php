<?php

namespace App\Entity;

use App\Repository\TrainerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: TrainerRepository::class)]
#[Broadcast]
class Trainer extends User implements UserInterface
{
    #[ORM\Column(length: 50)]
    private ?string $role = null;

    #[ORM\Column(length: 6, nullable: true)]
    private ?string $entranceCode = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $entranceCodeDate = null;

    #[ORM\OneToMany(targetEntity: Course::class, mappedBy: 'trainer')]
    private Collection $courses;

    #[ORM\ManyToOne(inversedBy: 'trainers')]
    private ?Sector $sector = null;

    #[ORM\OneToMany(targetEntity: Cohort::class, mappedBy: 'trainer')]
    private Collection $cohorts;

    /**
     * @var Collection<int, Quiz>
     */
    #[ORM\OneToMany(targetEntity: Quiz::class, mappedBy: 'trainer', orphanRemoval: true)]
    private Collection $quizzes;

    public function __construct()
    {
        $roles = $this->getRoles();
        $roles[] = "ROLE_TRAINER";
        $this->setRoles($roles);
        $this->courses = new ArrayCollection();
        $this->cohorts = new ArrayCollection();
        $this->quizzes = new ArrayCollection();
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getEntranceCode(): ?string
    {
        return $this->entranceCode;
    }

    public function setEntranceCode(?string $entranceCode): static
    {
        $this->entranceCode = $entranceCode;

        return $this;
    }

    public function getEntranceCodeDate(): ?\DateTimeImmutable
    {
        return $this->entranceCodeDate;
    }

    public function setEntranceCodeDate(?\DateTimeImmutable $entranceCodeDate): static
    {
        $this->entranceCodeDate = $entranceCodeDate;

        return $this;
    }

    /**
     * @return Collection<int, Course>
     */
    public function getCourses(): Collection
    {
        return $this->courses;
    }

    public function addCourse(Course $course): static
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->setTrainer($this);
        }

        return $this;
    }

    public function removeCourse(Course $course): static
    {
        if ($this->courses->removeElement($course)) {
            // set the owning side to null (unless already changed)
            if ($course->getTrainer() === $this) {
                $course->setTrainer(null);
            }
        }

        return $this;
    }

    public function getSector(): ?Sector
    {
        return $this->sector;
    }

    public function setSector(?Sector $sector): static
    {
        $this->sector = $sector;

        return $this;
    }

    /**
     * @return Collection<int, Cohort>
     */
    public function getCohorts(): Collection
    {
        return $this->cohorts;
    }

    public function addCohort(Cohort $cohort): static
    {
        if (!$this->cohorts->contains($cohort)) {
            $this->cohorts->add($cohort);
            $cohort->setTrainer($this);
        }

        return $this;
    }

    public function removeCohort(Cohort $cohort): static
    {
        if ($this->cohorts->removeElement($cohort)) {
            // set the owning side to null (unless already changed)
            if ($cohort->getTrainer() === $this) {
                $cohort->setTrainer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Quiz>
     */
    public function getQuizzes(): Collection
    {
        return $this->quizzes;
    }

    public function addQuiz(Quiz $quiz): static
    {
        if (!$this->quizzes->contains($quiz)) {
            $this->quizzes->add($quiz);
            $quiz->setTrainer($this);
        }

        return $this;
    }

    public function removeQuiz(Quiz $quiz): static
    {
        if ($this->quizzes->removeElement($quiz)) {
            // set the owning side to null (unless already changed)
            if ($quiz->getTrainer() === $this) {
                $quiz->setTrainer(null);
            }
        }

        return $this;
    }
}
