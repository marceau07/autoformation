<?php

namespace App\Entity;

use App\Repository\SurveyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: SurveyRepository::class)]
#[Broadcast]
class Survey
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 500)]
    private ?string $question = null;

    #[ORM\Column(length: 255)]
    private ?string $resume = null;

    #[ORM\OneToMany(targetEntity: SurveyTrainee::class, mappedBy: 'survey')]
    private Collection $surveyTrainees;

    public function __construct()
    {
        $this->surveyTrainees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(string $resume): static
    {
        $this->resume = $resume;

        return $this;
    }

    /**
     * @return Collection<int, SurveyTrainee>
     */
    public function getSurveyTrainees(): Collection
    {
        return $this->surveyTrainees;
    }

    public function addSurveyTrainee(SurveyTrainee $surveyTrainee): static
    {
        if (!$this->surveyTrainees->contains($surveyTrainee)) {
            $this->surveyTrainees->add($surveyTrainee);
            $surveyTrainee->setSurvey($this);
        }

        return $this;
    }

    public function removeSurveyTrainee(SurveyTrainee $surveyTrainee): static
    {
        if ($this->surveyTrainees->removeElement($surveyTrainee)) {
            // set the owning side to null (unless already changed)
            if ($surveyTrainee->getSurvey() === $this) {
                $surveyTrainee->setSurvey(null);
            }
        }

        return $this;
    }
}
