<?php

namespace App\Entity;

use App\Config\QuizType;
use App\Repository\QuizRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV7;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
#[Broadcast]
class QuizRow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $uuid = null;

    #[ORM\Column(length: 255)]
    private ?string $question = null;

    #[ORM\Column(length: 75, nullable: true)]
    private ?string $option1 = null;

    #[ORM\Column(length: 75, nullable: true)]
    private ?string $option2 = null;

    #[ORM\Column(length: 75, nullable: true)]
    private ?string $option3 = null;

    #[ORM\Column(length: 75, nullable: true)]
    private ?string $option4 = null;

    #[ORM\Column(enumType: QuizType::class)]
    private ?QuizType $quiz_type = null;

    #[ORM\Column]
    private ?int $timer = null;

    #[ORM\Column]
    private ?int $score = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hint = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $answer_explanation = null;

    #[ORM\ManyToOne(inversedBy: 'quizRows')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quiz $quiz = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $answer = null;

    /**
     * @var Collection<int, UserQuiz>
     */
    #[ORM\OneToMany(targetEntity: UserQuiz::class, mappedBy: 'quiz_row')]
    private Collection $userQuizzes;

    public function __construct()
    {
        $this->uuid = new UuidV7();
        $this->userQuizzes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getOption1(): ?string
    {
        return $this->option1;
    }

    public function setOption1(?string $option1): static
    {
        $this->option1 = $option1;

        return $this;
    }

    public function getOption2(): ?string
    {
        return $this->option2;
    }

    public function setOption2(?string $option2): static
    {
        $this->option2 = $option2;

        return $this;
    }

    public function getOption3(): ?string
    {
        return $this->option3;
    }

    public function setOption3(?string $option3): static
    {
        $this->option3 = $option3;

        return $this;
    }

    public function getOption4(): ?string
    {
        return $this->option4;
    }

    public function setOption4(?string $option4): static
    {
        $this->option4 = $option4;

        return $this;
    }

    public function getQuizType(): ?QuizType
    {
        return $this->quiz_type;
    }

    public function setQuizType(QuizType $quiz_type): static
    {
        $this->quiz_type = $quiz_type;

        return $this;
    }

    public function getTimer(): ?int
    {
        return $this->timer;
    }

    public function setTimer(int $timer): static
    {
        $this->timer = $timer;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getHint(): ?string
    {
        return $this->hint;
    }

    public function setHint(?string $hint): static
    {
        $this->hint = $hint;

        return $this;
    }

    public function getAnswerExplanation(): ?string
    {
        return $this->answer_explanation;
    }

    public function setAnswerExplanation(?string $answer_explanation): static
    {
        $this->answer_explanation = $answer_explanation;

        return $this;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): static
    {
        $this->quiz = $quiz;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(?string $answer): static
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * @return Collection<int, UserQuiz>
     */
    public function getUserQuizzes(): Collection
    {
        return $this->userQuizzes;
    }

    public function addUserQuiz(UserQuiz $userQuiz): static
    {
        if (!$this->userQuizzes->contains($userQuiz)) {
            $this->userQuizzes->add($userQuiz);
            $userQuiz->setQuizRow($this);
        }

        return $this;
    }

    public function removeUserQuiz(UserQuiz $userQuiz): static
    {
        if ($this->userQuizzes->removeElement($userQuiz)) {
            // set the owning side to null (unless already changed)
            if ($userQuiz->getQuizRow() === $this) {
                $userQuiz->setQuizRow(null);
            }
        }

        return $this;
    }
}
