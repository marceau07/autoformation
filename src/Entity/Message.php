<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Cache(usage: "NONSTRICT_READ_WRITE", region: "non_strict")]
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

    #[ORM\ManyToOne(inversedBy: 'sent_messages_people')]
    # The people who sent the message
    private ?User $send_people = null;

    #[ORM\ManyToOne(inversedBy: 'received_messages_people')]
    # The people who received the message
    private ?User $people = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    # The cohort to which the message belongs
    private ?Cohort $cohort = null;

    #[ORM\Column]
    private ?bool $readed = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    private ?self $original_message = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $document = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $mime_type = null;

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

    public function getSendPeople(): ?User
    {
        return $this->send_people;
    }

    public function setSendPeople(?User $send_people): static
    {
        $this->send_people = $send_people;

        return $this;
    }

    public function getPeople(): ?User
    {
        return $this->people;
    }

    public function setPeople(?User $people): static
    {
        $this->people = $people;

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

    public function getOriginalMessage(): ?self
    {
        return $this->original_message;
    }

    public function setOriginalMessage(?self $original_message): static
    {
        $this->original_message = $original_message;

        return $this;
    }

    public function getDocument(): ?string
    {
        return $this->document;
    }

    public function setDocument(?string $document): static
    {
        $this->document = $document;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mime_type;
    }

    public function setMimeType(?string $mime_type): static
    {
        $this->mime_type = $mime_type;

        return $this;
    }
}
