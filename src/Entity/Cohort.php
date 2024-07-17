<?php

namespace App\Entity;

use App\Repository\CohortRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV7;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: CohortRepository::class)]
#[Broadcast]
class Cohort
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 25)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $acronym = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $shield = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $documents = null;

    #[ORM\ManyToOne(inversedBy: 'cohorts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trainer $trainer = null;

    #[ORM\OneToMany(targetEntity: CourseCohort::class, mappedBy: 'cohort')]
    private Collection $courseCohorts;

    #[ORM\OneToMany(targetEntity: Trainee::class, mappedBy: 'cohort')]
    private Collection $trainees;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $finishDate = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $uuid = null;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'cohort')]
    private Collection $messages;

    public function __construct()
    {
        $this->courseCohorts = new ArrayCollection();
        $this->trainees = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $uuid = new UuidV7();
        $this->uuid = $uuid->toString();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAcronym(): ?string
    {
        return $this->acronym;
    }

    public function setAcronym(string $acronym): static
    {
        $this->acronym = $acronym;

        return $this;
    }

    public function getShield(): ?string
    {
        return $this->shield;
    }

    public function setShield(?string $shield): static
    {
        $this->shield = $shield;

        return $this;
    }

    public function getDocuments(): string
    {
        return json_encode($this->documents);
    }

    public function setDocuments(string $documents): static
    {
        $this->documents = $documents;

        return $this;
    }

    public function getTrainer(): ?Trainer
    {
        return $this->trainer;
    }

    public function setTrainer(?Trainer $trainer): static
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
            $courseCohort->setCohort($this);
        }

        return $this;
    }

    public function removeCourseCohort(CourseCohort $courseCohort): static
    {
        if ($this->courseCohorts->removeElement($courseCohort)) {
            // set the owning side to null (unless already changed)
            if ($courseCohort->getCohort() === $this) {
                $courseCohort->setCohort(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Trainee>
     */
    public function getTrainees(): Collection
    {
        return $this->trainees;
    }

    public function addTrainee(Trainee $trainee): static
    {
        if (!$this->trainees->contains($trainee)) {
            $this->trainees->add($trainee);
            $trainee->setCohort($this);
        }

        return $this;
    }

    public function removeTrainee(Trainee $trainee): static
    {
        if ($this->trainees->removeElement($trainee)) {
            // set the owning side to null (unless already changed)
            if ($trainee->getCohort() === $this) {
                $trainee->setCohort(null);
            }
        }

        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeImmutable $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getFinishDate(): ?\DateTimeImmutable
    {
        return $this->finishDate;
    }

    public function setFinishDate(\DateTimeImmutable $finishDate): static
    {
        $this->finishDate = $finishDate;

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

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setCohort($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getCohort() === $this) {
                $message->setCohort(null);
            }
        }

        return $this;
    }
}
