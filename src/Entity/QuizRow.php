<?php

namespace App\Entity;

use App\Config\QuizType;
use App\Repository\QuizRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
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

    #[ORM\Column(type: 'uuid')]
    private ?string $uuid = null;

    #[ORM\Column(length: 255)]
    private ?string $question = null;

    #[ORM\Column(length: 75, nullable: true)]
    private ?string $answer1 = null;

    #[ORM\Column(length: 75, nullable: true)]
    private ?string $answer2 = null;

    #[ORM\Column(length: 75, nullable: true)]
    private ?string $answer3 = null;

    #[ORM\Column(length: 75, nullable: true)]
    private ?string $answer4 = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $answer_short_text = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $answer_long_text = null;

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

    public function __construct()
    {
        $this->uuid = new UuidV7();
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

    public function getAnswer1(): ?string
    {
        return $this->answer1;
    }

    public function setAnswer1(?string $answer1): static
    {
        $this->answer1 = $answer1;

        return $this;
    }

    public function getAnswer2(): ?string
    {
        return $this->answer2;
    }

    public function setAnswer2(?string $answer2): static
    {
        $this->answer2 = $answer2;

        return $this;
    }

    public function getAnswer3(): ?string
    {
        return $this->answer3;
    }

    public function setAnswer3(?string $answer3): static
    {
        $this->answer3 = $answer3;

        return $this;
    }

    public function getAnswer4(): ?string
    {
        return $this->answer4;
    }

    public function setAnswer4(?string $answer4): static
    {
        $this->answer4 = $answer4;

        return $this;
    }

    public function getAnswerShortText(): ?string
    {
        return $this->answer_short_text;
    }

    public function setAnswerShortText(?string $answer_short_text): static
    {
        $this->answer_short_text = $answer_short_text;

        return $this;
    }

    public function getAnswerLongText(): ?string
    {
        return $this->answer_long_text;
    }

    public function setAnswerLongText(?string $answer_long_text): static
    {
        $this->answer_long_text = $answer_long_text;

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
}
