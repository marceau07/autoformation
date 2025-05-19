<?php

namespace App\Entity;

use App\Repository\CoordinatorRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Cache(usage: "NONSTRICT_READ_WRITE", region: "non_strict")]
#[ORM\Entity(repositoryClass: CoordinatorRepository::class)]
#[Broadcast]
class Coordinator extends User implements UserInterface
{
    #[ORM\Column(length: 50)]
    private ?string $role = null;

    #[ORM\Column(length: 6, nullable: true)]
    private ?string $entranceCode = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $entranceCodeDate = null;

    #[ORM\ManyToOne(inversedBy: 'coordinators')]
    private ?Responsible $responsible = null;

    #[ORM\OneToMany(targetEntity: Trainer::class, mappedBy: 'coordinator')]
    private Collection $trainers;

    public function __construct()
    {
        $roles = $this->getRoles();
        $roles[] = "ROLE_COORDINATOR";
        $this->setRoles($roles);
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getEntranceCode(): ?string
    {
        return $this->entranceCode;
    }

    public function setEntranceCode(?string $entranceCode): static
    {
        $this->entranceCode = $entranceCode;

        return $this;
    }

    public function getEntranceCodeDate(): ?\DateTimeImmutable
    {
        return $this->entranceCodeDate;
    }

    public function setEntranceCodeDate(?\DateTimeImmutable $entranceCodeDate): static
    {
        $this->entranceCodeDate = $entranceCodeDate;

        return $this;
    }

    public function getResponsible(): ?Responsible
    {
        return $this->responsible;
    }

    public function setResponsible(?Responsible $responsible): static
    {
        $this->responsible = $responsible;

        return $this;
    }

    public function __toString()
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    /**
     * @return Collection<int, Trainer>
     */
    public function getTrainers(): Collection
    {
        return $this->trainers;
    }

    public function addTrainer(Trainer $trainer): static
    {
        if (!$this->trainers->contains($trainer)) {
            $this->trainers->add($trainer);
        }

        return $this;
    }

    public function removeTrainer(Trainer $trainer): static
    {
        $this->trainers->removeElement($trainer);

        return $this;
    }
}
