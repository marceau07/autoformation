<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
#[Broadcast]
class Course
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('course_search')]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups('course_search')]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $synopsis = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups('course_search')]
    private ?string $keywords = null;

    #[ORM\Column(length: 255)]
    #[Groups('course_search')]
    private ?string $link = null;

    #[ORM\Column]
    #[Groups('course_search')]
    private ?int $position = null;

    #[ORM\ManyToOne(inversedBy: 'courses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('course_search')]
    private ?CourseModule $module = null;

    #[ORM\ManyToOne(inversedBy: null)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $trainer = null;

    #[ORM\OneToMany(targetEntity: CourseCohort::class, mappedBy: 'course')]
    private Collection $courseCohorts;

    #[ORM\OneToMany(targetEntity: CourseTrainee::class, mappedBy: 'course')]
    private Collection $courseTrainees;

    #[ORM\Column]
    private ?int $visitors = null;

    #[ORM\OneToMany(targetEntity: CourseResource::class, mappedBy: 'course')]
    private Collection $courseResources;

    public function __construct()
    {
        $this->courseCohorts = new ArrayCollection();
        $this->courseTrainees = new ArrayCollection();
        $this->courseResources = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSynopsis(): ?string
    {
        return $this->synopsis;
    }

    public function setSynopsis(string $synopsis): static
    {
        $this->synopsis = $synopsis;

        return $this;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(string $keywords): static
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): static
    {
        $this->link = $link;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getModule(): ?CourseModule
    {
        return $this->module;
    }

    public function setModule(?CourseModule $module): static
    {
        $this->module = $module;

        return $this;
    }

    public function getTrainer(): ?User
    {
        return $this->trainer;
    }

    public function setTrainer(?User $trainer): static
    {
        $this->trainer = $trainer;

        return $this;
    }

    /**
     * @return Collection<int, CourseCohort>
     */
    public function getCourseCohorts(): Collection
    {
        return $this->courseCohorts;
    }

    public function addCourseCohort(CourseCohort $courseCohort): static
    {
        if (!$this->courseCohorts->contains($courseCohort)) {
            $this->courseCohorts->add($courseCohort);
            $courseCohort->setCourse($this);
        }

        return $this;
    }

    public function removeCourseCohort(CourseCohort $courseCohort): static
    {
        if ($this->courseCohorts->removeElement($courseCohort)) {
            // set the owning side to null (unless already changed)
            if ($courseCohort->getCourse() === $this) {
                $courseCohort->setCourse(null);
            }
        }

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
            $courseTrainee->setCourse($this);
        }

        return $this;
    }

    public function removeCourseTrainee(CourseTrainee $courseTrainee): static
    {
        if ($this->courseTrainees->removeElement($courseTrainee)) {
            // set the owning side to null (unless already changed)
            if ($courseTrainee->getCourse() === $this) {
                $courseTrainee->setCourse(null);
            }
        }

        return $this;
    }

    public function getVisitors(): ?int
    {
        return $this->visitors;
    }

    public function setVisitors(int $visitors): static
    {
        $this->visitors = $visitors;

        return $this;
    }

    /**
     * @return Collection<int, CourseResource>
     */
    public function getCourseResources(): Collection
    {
        return $this->courseResources;
    }

    public function addCourseResource(CourseResource $courseResource): static
    {
        if (!$this->courseResources->contains($courseResource)) {
            $this->courseResources->add($courseResource);
            $courseResource->setCourse($this);
        }

        return $this;
    }

    public function removeCourseResource(CourseResource $courseResource): static
    {
        if ($this->courseResources->removeElement($courseResource)) {
            // set the owning side to null (unless already changed)
            if ($courseResource->getCourse() === $this) {
                $courseResource->setCourse(null);
            }
        }

        return $this;
    }
}
