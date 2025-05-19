<?php

namespace App\Entity;

use App\Repository\SectorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Cache(usage: "NONSTRICT_READ_WRITE", region: "non_strict")]
#[ORM\Entity(repositoryClass: SectorRepository::class)]
#[Broadcast]
class Sector
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $label = null;

    #[ORM\Column(length: 150)]
    private ?string $logo = null;

    #[ORM\OneToMany(targetEntity: Faq::class, mappedBy: 'sector')]
    private Collection $faqs;

    /**
     * @var Collection<int, Responsible>
     */
    #[ORM\OneToMany(targetEntity: Responsible::class, mappedBy: 'sector')]
    private Collection $responsibles;

    public function __construct()
    {
        $this->faqs = new ArrayCollection();
        $this->responsibles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return Collection<int, Faq>
     */
    public function getFaqs(): Collection
    {
        return $this->faqs;
    }

    public function addFaq(Faq $faq): static
    {
        if (!$this->faqs->contains($faq)) {
            $this->faqs->add($faq);
            $faq->setSector($this);
        }

        return $this;
    }

    public function removeFaq(Faq $faq): static
    {
        if ($this->faqs->removeElement($faq)) {
            // set the owning side to null (unless already changed)
            if ($faq->getSector() === $this) {
                $faq->setSector(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Responsible>
     */
    public function getResponsibles(): Collection
    {
        return $this->responsibles;
    }

    public function addResponsible(Responsible $responsible): static
    {
        if (!$this->responsibles->contains($responsible)) {
            $this->responsibles->add($responsible);
            $responsible->setSector($this);
        }

        return $this;
    }

    public function removeResponsible(Responsible $responsible): static
    {
        if ($this->responsibles->removeElement($responsible)) {
            // set the owning side to null (unless already changed)
            if ($responsible->getSector() === $this) {
                $responsible->setSector(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->label;
    }
}
