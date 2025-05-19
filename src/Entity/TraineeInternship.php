<?php

namespace App\Entity;

use App\Repository\TraineeInternshipRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Filesystem\Filesystem;

#[ORM\Cache(usage: "NONSTRICT_READ_WRITE", region: "non_strict")]
#[ORM\Entity(repositoryClass: TraineeInternshipRepository::class)]
class TraineeInternship
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'traineeInternships')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Internship $internship = null;

    #[ORM\ManyToOne(inversedBy: 'traineeInternships')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trainee $trainee = null;

    #[ORM\Column(nullable: true)]
    private ?bool $agreement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $agreement_link = null;

    #[ORM\Column(nullable: true)]
    private ?bool $certificate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $certificate_link = null;

    #[ORM\Column(nullable: true)]
    private ?bool $evaluation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $evaluation_link = null;

    #[ORM\ManyToOne(inversedBy: 'traineeInternships')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CohortInternship $cohort_internship = null;

    public function __construct()
    {
        $this->agreement = false; // Valeur par défaut quand on initialise un stage pour un.e stagiaire
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInternship(): ?Internship
    {
        return $this->internship;
    }

    public function setInternship(?Internship $internship): static
    {
        $this->internship = $internship;

        return $this;
    }

    public function getTrainee(): ?Trainee
    {
        return $this->trainee;
    }

    public function setTrainee(?Trainee $trainee): static
    {
        $this->trainee = $trainee;

        return $this;
    }

    public function isAgreement(): ?bool
    {
        return $this->agreement;
    }

    public function setAgreement(null|bool $agreement): static
    {
        $this->agreement = $agreement;

        $fs = new Filesystem();
        if ($agreement && $fs->exists('internships/tmp/Convention_de_stage_' . strtoupper($this->trainee->getLastName()) . '_' . ucwords($this->trainee->getFirstName()) . '.pdf')) {
            $link = $this->getCohortInternship()->getUuid() . '_' . md5(string: uniqid());
            $this->setAgreementLink($link);
            $fs->copy('internships/tmp/Convention_de_stage_' . strtoupper($this->trainee->getLastName()) . '_' . ucwords($this->trainee->getFirstName()) . '.pdf', 'internships/' . $this->trainee->getUsername() . '/Convention_de_stage_' . $link . '.pdf');
            $fs->remove('internships/tmp/Convention_de_stage_' . strtoupper($this->trainee->getLastName()) . '_' . ucwords($this->trainee->getFirstName()) . '.pdf');
        } elseif (!$agreement && !$fs->exists('internships/tmp/Convention_de_stage_' . strtoupper($this->trainee->getLastName()) . '_' . ucwords($this->trainee->getFirstName()) . '.pdf')) {
            $fs->remove('internships/' . $this->trainee->getUsername() . '/Convention_de_stage_' . $this->getAgreementLink() . '.pdf');
            $this->setAgreementLink(null);
            $this->agreement = null;
        }

        $notification = new Notification();
        $notification->setOrigin($this->getTrainee()->getUserIdentifier());
        $notification->setMessage("convention");
        if ($this->isAgreement() === true) {
            $notification->setLink("/../internships/" . $this->getTrainee()->getUserIdentifier() . '/Convention_de_stage_' . $this->getAgreementLink() . '.pdf');
        } else {
            $notification->setLink("/../internships/tmp/" . $this->getAgreementLink());
        }
        $notification->setCategory("new_internship");
        $notification->setDate(new \DateTimeImmutable());
        $notification->setUser($this->getTrainee()->getCohort()->getTrainer());
        $this->getTrainee()->getCohort()->getTrainer()->addNotification($notification);

        return $this;
    }

    public function getAgreementLink(): ?string
    {
        return $this->agreement_link;
    }

    public function setAgreementLink(?string $agreement_link): static
    {
        $this->agreement_link = $agreement_link;

        return $this;
    }

    public function isCertificate(): ?bool
    {
        return $this->certificate;
    }

    public function setCertificate(null|bool $certificate): static
    {
        $this->certificate = $certificate;

        $fs = new Filesystem();
        if ($certificate && $fs->exists('internships/tmp/Attestation_de_stage_' . strtoupper($this->trainee->getLastName()) . '_' . ucwords($this->trainee->getFirstName()) . '.pdf')) {
            $link = $this->getCohortInternship()->getUuid() . '_' . md5(string: uniqid());
            $this->setCertificateLink($link);
            $fs->copy('internships/tmp/Attestation_de_stage_' . strtoupper($this->trainee->getLastName()) . '_' . ucwords($this->trainee->getFirstName()) . '.pdf', 'internships/' . $this->trainee->getUsername() . '/Attestation_de_stage_' . $link . '.pdf');
            $fs->remove('internships/tmp/Attestation_de_stage_' . strtoupper($this->trainee->getLastName()) . '_' . ucwords($this->trainee->getFirstName()) . '.pdf');
        } elseif (!$certificate && $fs->exists('internships/tmp/Attestation_de_stage_' . strtoupper($this->trainee->getLastName()) . '_' . ucwords($this->trainee->getFirstName()) . '.pdf')) {
            $this->certificate = 0;
            $this->setCertificateLink('Attestation_de_stage_' . strtoupper($this->trainee->getLastName()) . '_' . ucwords($this->trainee->getFirstName()) . '.pdf');
        } elseif (!$certificate && !$fs->exists('internships/tmp/Attestation_de_stage_' . strtoupper($this->trainee->getLastName()) . '_' . ucwords($this->trainee->getFirstName()) . '.pdf')) {
            $this->certificate = null;
            $this->setCertificateLink(null);
            $fs->remove('internships/' . $this->trainee->getUsername() . '/Attestation_de_stage_' . $this->getCertificateLink() . '.pdf');
        }

        $notification = new Notification();
        $notification->setOrigin($this->getTrainee()->getUserIdentifier());
        $notification->setMessage("attestation");
        if ($this->isCertificate() === true) {
            $notification->setLink("/../internships/" . $this->getTrainee()->getUserIdentifier() . '/Attestation_de_stage_' . $this->getCertificateLink() . '.pdf');
        } else {
            $notification->setLink("/../internships/tmp/" . $this->getCertificateLink());
        }
        $notification->setCategory("new_internship");
        $notification->setDate(new \DateTimeImmutable());
        $notification->setUser($this->getTrainee()->getCohort()->getTrainer());
        $this->getTrainee()->getCohort()->getTrainer()->addNotification($notification);

        return $this;
    }

    public function getCertificateLink(): ?string
    {
        return $this->certificate_link;
    }

    public function setCertificateLink(?string $certificate_link): static
    {
        $this->certificate_link = $certificate_link;

        return $this;
    }

    public function isEvaluation(): ?bool
    {
        return $this->evaluation;
    }

    public function setEvaluation(null|bool $evaluation): static
    {
        $this->evaluation = $evaluation;

        $fs = new Filesystem();
        if ($evaluation && $fs->exists('internships/tmp/Evaluation_de_stage_' . strtoupper($this->trainee->getLastName()) . '_' . ucwords($this->trainee->getFirstName()) . '.pdf')) {
            $link = $this->getCohortInternship()->getUuid() . '_' . md5(string: uniqid());
            $this->setEvaluationLink($link);
            $fs->copy('internships/tmp/Evaluation_de_stage_' . strtoupper($this->trainee->getLastName()) . '_' . ucwords($this->trainee->getFirstName()) . '.pdf', 'internships/' . $this->trainee->getUsername() . '/Evaluation_de_stage_' . $link . '.pdf');
            $fs->remove('internships/tmp/Evaluation_de_stage_' . strtoupper($this->trainee->getLastName()) . '_' . ucwords($this->trainee->getFirstName()) . '.pdf');
        } elseif (!$evaluation && $fs->exists('internships/tmp/Evaluation_de_stage_' . strtoupper($this->trainee->getLastName()) . '_' . ucwords($this->trainee->getFirstName()) . '.pdf')) {
            $this->evaluation = 0;
            $this->setEvaluationLink('Evaluation_de_stage_' . strtoupper($this->trainee->getLastName()) . '_' . ucwords($this->trainee->getFirstName()) . '.pdf');
        } elseif (!$evaluation && !$fs->exists('internships/tmp/Evaluation_de_stage_' . strtoupper($this->trainee->getLastName()) . '_' . ucwords($this->trainee->getFirstName()) . '.pdf')) {
            $this->evaluation = null;
            $fs->remove('internships/' . $this->trainee->getUsername() . '/Evaluation_de_stage_' . $this->getEvaluationLink() . '.pdf');
            $this->setEvaluationLink(null);
        }

        $notification = new Notification();
        $notification->setOrigin($this->getTrainee()->getUserIdentifier());
        $notification->setMessage("evaluation");
        if ($this->isEvaluation() === true) {
            $notification->setLink("/../internships/" . $this->getTrainee()->getUserIdentifier() . '/Evaluation_de_stage_' . $this->getEvaluationLink() . '.pdf');
        } else {
            $notification->setLink("/../internships/tmp/" . $this->getEvaluationLink());
        }
        $notification->setCategory("new_internship");
        $notification->setDate(new \DateTimeImmutable());
        $notification->setUser($this->getTrainee()->getCohort()->getTrainer());
        $this->getTrainee()->getCohort()->getTrainer()->addNotification($notification);

        return $this;
    }

    public function getEvaluationLink(): ?string
    {
        return $this->evaluation_link;
    }

    public function setEvaluationLink(?string $evaluation_link): static
    {
        $this->evaluation_link = $evaluation_link;

        return $this;
    }

    public function getCohortInternship(): ?CohortInternship
    {
        return $this->cohort_internship;
    }

    public function setCohortInternship(?CohortInternship $cohort_internship): static
    {
        $this->cohort_internship = $cohort_internship;

        return $this;
    }
}
