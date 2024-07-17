<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[Broadcast]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date = null;

    #[ORM\ManyToOne(inversedBy: 'sent_messages_trainer')]
    # The trainer who sent the message
    private ?User $send_trainer = null;

    #[ORM\ManyToOne(inversedBy: 'sent_messages_trainee')]
    # The trainee who sent the message
    private ?User $send_trainee = null;

    #[ORM\ManyToOne(inversedBy: 'received_messages_trainee')]
    # The trainee who received the message
    private ?User $trainee = null;

    #[ORM\ManyToOne(inversedBy: 'received_messages_trainer')]
    # The trainer who received the message
    private ?User $trainer = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    # The cohort to which the message belongs
    private ?Cohort $cohort = null;

    #[ORM\Column]
    private ?bool $readed = null;

    public function __construct(bool $readed = false)
    {
        $this->readed = $readed;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getSendTrainer(): ?User
    {
        return $this->send_trainer;
    }

    public function setSendTrainer(?User $send_trainer): static
    {
        $this->send_trainer = $send_trainer;

        return $this;
    }

    public function getSendTrainee(): ?User
    {
        return $this->send_trainee;
    }

    public function setSendTrainee(?User $send_trainee): static
    {
        $this->send_trainee = $send_trainee;

        return $this;
    }

    public function getTrainee(): ?User
    {
        return $this->trainee;
    }

    public function setTrainee(?User $trainee): static
    {
        $this->trainee = $trainee;

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

    public function getCohort(): ?Cohort
    {
        return $this->cohort;
    }

    public function setCohort(?Cohort $cohort): static
    {
        $this->cohort = $cohort;

        return $this;
    }

    public function isReaded(): ?bool
    {
        return $this->readed;
    }

    public function setReaded(bool $readed): static
    {
        $this->readed = $readed;

        return $this;
    }
}
