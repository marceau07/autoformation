<?php

namespace App\Entity;

use App\Repository\TrainerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: TrainerRepository::class)]
#[Broadcast]
class Trainer extends User implements UserInterface
{
    #[ORM\Column(length: 50)]
    private ?string $role = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 6, nullable: true)]
    private ?string $entranceCode = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $entranceCodeDate = null;

    #[ORM\OneToMany(targetEntity: Session::class, mappedBy: 'trainer')]
    private Collection $sessions;

    #[ORM\OneToMany(targetEntity: Course::class, mappedBy: 'trainer')]
    private Collection $courses;
    
    public function __construct() {
        $roles = $this->getRoles();
        $roles[] = "ROLE_TRAINER";
        $this->setRoles($roles);
        $this->sessions = new ArrayCollection();
        $this->courses = new ArrayCollection();
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

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

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

    /**
     * @return Collection<int, Session>
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): static
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->setTrainer($this);
        }

        return $this;
    }

    public function removeSession(Session $session): static
    {
        if ($this->sessions->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getTrainer() === $this) {
                $session->setTrainer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Course>
     */
    public function getCourses(): Collection
    {
        return $this->courses;
    }

    public function addCourse(Course $course): static
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->setTrainer($this);
        }

        return $this;
    }

    public function removeCourse(Course $course): static
    {
        if ($this->courses->removeElement($course)) {
            // set the owning side to null (unless already changed)
            if ($course->getTrainer() === $this) {
                $course->setTrainer(null);
            }
        }

        return $this;
    }
}
