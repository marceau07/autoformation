<?php

namespace App\Entity;

use App\Repository\ResponsibleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Cache(usage: "NONSTRICT_READ_WRITE", region: "non_strict")]
#[ORM\Entity(repositoryClass: ResponsibleRepository::class)]
#[Broadcast]
class Responsible extends User implements UserInterface
{
    #[ORM\Column(length: 50)]
    private ?string $role = null;

    #[ORM\Column(length: 6, nullable: true)]
    private ?string $entranceCode = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $entranceCodeDate = null;

    #[ORM\ManyToOne(inversedBy: 'responsibles')]
    private ?Sector $sector = null;

    #[ORM\OneToMany(targetEntity: Coordinator::class, mappedBy: 'responsible')]
    private Collection $coordinators;

    public function __construct()
    {
        $roles = $this->getRoles();
        $roles[] = "ROLE_RESPONSIBLE";
        $this->setRoles($roles);
        $this->coordinators = new ArrayCollection();
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

    public function getSector(): ?Sector
    {
        return $this->sector;
    }

    public function setSector(?Sector $sector): static
    {
        $this->sector = $sector;

        return $this;
    }

    public function __toString()
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    /**
     * @return Collection<int, Coordinator>
     */
    public function getCoordinators(): Collection
    {
        return $this->coordinators;
    }

    public function addCoordinator(Coordinator $coordinator): static
    {
        if (!$this->coordinators->contains($coordinator)) {
            $this->coordinators->add($coordinator);
        }

        return $this;
    }

    public function removeCoordinator(Coordinator $coordinator): static
    {
        $this->coordinators->removeElement($coordinator);

        return $this;
    }
}
