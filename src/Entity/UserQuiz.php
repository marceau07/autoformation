<?php

namespace App\Entity;

use App\Repository\UserQuizRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: UserQuizRepository::class)]
#[Broadcast]
class UserQuiz
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userQuizzes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'userQuizzes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?QuizRow $quiz_row = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $answer = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $finish_date = null;

    public function __construct()
    {
        $this->finish_date = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getQuizRow(): ?QuizRow
    {
        return $this->quiz_row;
    }

    public function setQuizRow(?QuizRow $quiz_row): static
    {
        $this->quiz_row = $quiz_row;

        return $this;
    }

    public function getAnswer(): string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): static
    {
        $this->answer = $answer;

        return $this;
    }

    public function getFinishDate(): ?\DateTimeImmutable
    {
        return $this->finish_date;
    }

    public function setFinishDate(\DateTimeImmutable $finish_date): static
    {
        $this->finish_date = $finish_date;

        return $this;
    }
}
