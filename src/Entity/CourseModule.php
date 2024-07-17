<?php

namespace App\Entity;

use App\Repository\CourseModuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: CourseModuleRepository::class)]
#[Broadcast]
class CourseModule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('course_search')]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups('course_search')]
    private ?string $label = null;

    #[ORM\Column]
    #[Groups('course_search')]
    private ?int $position = null;

    #[ORM\Column(type: Types::GUID)]
    #[Groups('course_search')]
    private ?string $uuid = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('course_search')]
    private ?string $illustration = null;

    #[ORM\OneToMany(targetEntity: Course::class, mappedBy: 'module')]
    private Collection $courses;

    public function __construct()
    {
        $this->courses = new ArrayCollection();
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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

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

    public function getIllustration(): ?string
    {
        return $this->illustration;
    }

    public function setIllustration(?string $illustration): static
    {
        $this->illustration = $illustration;

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
            $course->setModule($this);
        }

        return $this;
    }

    public function removeCourse(Course $course): static
    {
        if ($this->courses->removeElement($course)) {
            // set the owning side to null (unless already changed)
            if ($course->getModule() === $this) {
                $course->setModule(null);
            }
        }

        return $this;
    }
}
