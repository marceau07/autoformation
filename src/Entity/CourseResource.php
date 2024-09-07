<?php

namespace App\Entity;

use App\Repository\CourseResourceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: CourseResourceRepository::class)]
#[Broadcast]
class CourseResource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $resume = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $link = null;

    #[ORM\Column(length: 30)]
    private ?string $type = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $record = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $recordLink = null;

    #[ORM\ManyToOne(inversedBy: 'courseResources')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Course $course = null;

    /**
     * @var Collection<int, TraineeResource>
     */
    #[ORM\OneToMany(targetEntity: TraineeResource::class, mappedBy: 'resource')]
    private Collection $traineeResources;

    public function __construct()
    {
        $this->traineeResources = new ArrayCollection();
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

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(string $resume): static
    {
        $this->resume = $resume;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): static
    {
        $this->link = $link;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getRecord(): ?string
    {
        return $this->record;
    }

    public function setRecord(?string $record): static
    {
        $this->record = $record;

        return $this;
    }

    public function getRecordLink(): ?string
    {
        return $this->recordLink;
    }

    public function setRecordLink(?string $recordLink): static
    {
        $this->recordLink = $recordLink;

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
            $traineeResource->setResource($this);
        }

        return $this;
    }

    public function removeTraineeResource(TraineeResource $traineeResource): static
    {
        if ($this->traineeResources->removeElement($traineeResource)) {
            // set the owning side to null (unless already changed)
            if ($traineeResource->getResource() === $this) {
                $traineeResource->setResource(null);
            }
        }

        return $this;
    }
}
