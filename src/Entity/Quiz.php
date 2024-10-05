<?php

namespace App\Entity;

use App\Repository\QuizRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
#[Broadcast]
class Quiz
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, QuizRow>
     */
    #[ORM\OneToMany(targetEntity: QuizRow::class, mappedBy: 'quiz', orphanRemoval: true)]
    private Collection $quizRows;

    #[ORM\ManyToOne(inversedBy: 'quizzes')]
    private ?QuizTheme $theme = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    /**
     * @var Collection<int, QuizShare>
     */
    #[ORM\OneToMany(targetEntity: QuizShare::class, mappedBy: 'quiz', orphanRemoval: true)]
    private Collection $quizShares;

    #[ORM\Column(type: 'uuid')]
    private ?string $uuid = null;

    #[ORM\ManyToOne(inversedBy: 'quizzes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trainer $trainer = null;

    public function __construct()
    {
        $this->quizRows = new ArrayCollection();
        $this->quizShares = new ArrayCollection();
        $this->uuid = new UuidV7();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, QuizRow>
     */
    public function getQuizRows(): Collection
    {
        return $this->quizRows;
    }

    public function addQuizRow(QuizRow $quizRow): static
    {
        if (!$this->quizRows->contains($quizRow)) {
            $this->quizRows->add($quizRow);
            $quizRow->setQuiz($this);
        }

        return $this;
    }

    public function removeQuizRow(QuizRow $quizRow): static
    {
        if ($this->quizRows->removeElement($quizRow)) {
            // set the owning side to null (unless already changed)
            if ($quizRow->getQuiz() === $this) {
                $quizRow->setQuiz(null);
            }
        }

        return $this;
    }

    public function getTheme(): ?QuizTheme
    {
        return $this->theme;
    }

    public function setTheme(?QuizTheme $theme): static
    {
        $this->theme = $theme;

        return $this;
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

    /**
     * @return Collection<int, QuizShare>
     */
    public function getQuizShares(): Collection
    {
        return $this->quizShares;
    }

    public function addQuizShare(QuizShare $quizShare): static
    {
        if (!$this->quizShares->contains($quizShare)) {
            $this->quizShares->add($quizShare);
            $quizShare->setQuiz($this);
        }

        return $this;
    }

    public function removeQuizShare(QuizShare $quizShare): static
    {
        if ($this->quizShares->removeElement($quizShare)) {
            // set the owning side to null (unless already changed)
            if ($quizShare->getQuiz() === $this) {
                $quizShare->setQuiz(null);
            }
        }

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

    public function getTrainer(): ?Trainer
    {
        return $this->trainer;
    }

    public function setTrainer(?Trainer $trainer): static
    {
        $this->trainer = $trainer;

        return $this;
    }
}
