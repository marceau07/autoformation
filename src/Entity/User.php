<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\UuidV7;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\InheritanceType("JOINED")]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['trainee_search', 'trainer_search'])]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Groups(['trainee_search', 'trainer_search'])]
    private ?string $username = null;

    #[ORM\Column(length: 75)]
    #[Groups(['trainee_search', 'trainer_search'])]
    private ?string $lastName = null;

    #[ORM\Column(length: 75)]
    #[Groups(['trainee_search', 'trainer_search'])]
    private ?string $firstName = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column]
    private bool $activated;

    #[ORM\Column(length: 6, nullable: true)]
    private ?string $tmpCode = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $tmpCodeDate = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['trainee_search', 'trainer_search'])]
    private ?Avatar $avatar = null;

    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'user')]
    private Collection $notifications;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $signature = null;

    /**
     * @var Collection<int, TraineeResource>
     */
    #[ORM\OneToMany(targetEntity: TraineeResource::class, mappedBy: 'trainee')]
    private Collection $userResources;

    /**
     * @var Collection<int, Feedback>
     */
    #[ORM\OneToMany(targetEntity: Feedback::class, mappedBy: 'user')]
    private Collection $feedback;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['trainee_search', 'trainer_search'])]
    private ?string $uuid = null;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'send_trainer')]
    private Collection $sent_messages_trainer;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'trainer')]
    private Collection $received_messages_trainer;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'send_trainee')]
    private Collection $sent_messages_trainee;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'trainee')]
    private Collection $received_messages_trainee;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $phoneNumber = null;

    public function __construct()
    {
        $this->activated = true;
        $this->notifications = new ArrayCollection();
        $this->userResources = new ArrayCollection();
        $this->feedback = new ArrayCollection();
        $this->sent_messages_trainer = new ArrayCollection();
        $this->received_messages_trainer = new ArrayCollection();
        $this->sent_messages_trainee = new ArrayCollection();
        $this->received_messages_trainee = new ArrayCollection();
        $uuid = new UuidV7();
        $this->uuid = $uuid->toString();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function isActivated(): bool
    {
        return $this->activated;
    }

    public function setActivated(bool $activated): static
    {
        $this->activated = $activated;

        return $this;
    }

    public function getTmpCode(): ?string
    {
        return $this->tmpCode;
    }

    public function setTmpCode(?string $tmpCode): static
    {
        $this->tmpCode = $tmpCode;

        return $this;
    }

    public function getTmpCodeDate(): ?\DateTimeImmutable
    {
        return $this->tmpCodeDate;
    }

    public function setTmpCodeDate(?\DateTimeImmutable $tmpCodeDate): static
    {
        $this->tmpCodeDate = $tmpCodeDate;

        return $this;
    }

    public function getAvatar(): ?Avatar
    {
        return $this->avatar;
    }

    public function setAvatar(?Avatar $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }

    public function getSignature(): ?string
    {
        return $this->signature;
    }

    public function setSignature(?string $signature): static
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * @return Collection<int, TraineeResource>
     */
    public function getTraineeResources(): Collection
    {
        return $this->userResources;
    }

    public function addTraineeResource(TraineeResource $traineeResource): static
    {
        if (!$this->userResources->contains($traineeResource)) {
            $this->userResources->add($traineeResource);
            $traineeResource->setTrainee($this);
        }

        return $this;
    }

    public function removeTraineeResource(TraineeResource $traineeResource): static
    {
        if ($this->userResources->removeElement($traineeResource)) {
            // set the owning side to null (unless already changed)
            if ($traineeResource->getTrainee() === $this) {
                $traineeResource->setTrainee(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Feedback>
     */
    public function getFeedback(): Collection
    {
        return $this->feedback;
    }

    public function addFeedback(Feedback $feedback): static
    {
        if (!$this->feedback->contains($feedback)) {
            $this->feedback->add($feedback);
            $feedback->setUser($this);
        }

        return $this;
    }

    public function removeFeedback(Feedback $feedback): static
    {
        if ($this->feedback->removeElement($feedback)) {
            // set the owning side to null (unless already changed)
            if ($feedback->getUser() === $this) {
                $feedback->setUser(null);
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

    /**
     * @return Collection<int, Message>
     */
    public function getSentMessagesTrainer(): Collection
    {
        return $this->sent_messages_trainer;
    }

    public function addSentMessageTrainer(Message $sentMessage): static
    {
        if (!$this->sent_messages_trainer->contains($sentMessage)) {
            $this->sent_messages_trainer->add($sentMessage);
            $sentMessage->setSendTrainer($this);
        }

        return $this;
    }

    public function removeSentMessageTrainer(Message $sentMessage): static
    {
        if ($this->sent_messages_trainer->removeElement($sentMessage)) {
            // set the owning side to null (unless already changed)
            if ($sentMessage->getSendTrainer() === $this) {
                $sentMessage->setSendTrainer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getReceivedMessagesTrainer(): Collection
    {
        return $this->received_messages_trainer;
    }

    public function addReceivedMessageTrainer(Message $receivedMessage): static
    {
        if (!$this->received_messages_trainer->contains($receivedMessage)) {
            $this->received_messages_trainer->add($receivedMessage);
            $receivedMessage->setTrainer($this);
        }

        return $this;
    }

    public function removeReceivedMessageTrainer(Message $receivedMessage): static
    {
        if ($this->received_messages_trainer->removeElement($receivedMessage)) {
            // set the owning side to null (unless already changed)
            if ($receivedMessage->getTrainer() === $this) {
                $receivedMessage->setTrainer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getSentMessagesTrainee(): Collection
    {
        return $this->sent_messages_trainee;
    }

    public function addSentMessageTrainee(Message $sentMessage): static
    {
        if (!$this->sent_messages_trainee->contains($sentMessage)) {
            $this->sent_messages_trainee->add($sentMessage);
            $sentMessage->setSendTrainee($this);
        }

        return $this;
    }

    public function removeSentMessageTrainee(Message $sentMessage): static
    {
        if ($this->sent_messages_trainee->removeElement($sentMessage)) {
            // set the owning side to null (unless already changed)
            if ($sentMessage->getSendTrainee() === $this) {
                $sentMessage->setSendTrainee(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getReceivedMessagesTrainee(): Collection
    {
        return $this->received_messages_trainee;
    }

    public function addReceivedMessageTrainee(Message $receivedMessage): static
    {
        if (!$this->received_messages_trainee->contains($receivedMessage)) {
            $this->received_messages_trainee->add($receivedMessage);
            $receivedMessage->setTrainee($this);
        }

        return $this;
    }

    public function removeReceivedMessageTrainee(Message $receivedMessage): static
    {
        if ($this->received_messages_trainee->removeElement($receivedMessage)) {
            // set the owning side to null (unless already changed)
            if ($receivedMessage->getTrainee() === $this) {
                $receivedMessage->setTrainee(null);
            }
        }

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }
}
