<?php

namespace App\Entity;

use App\Repository\QuizShareRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: QuizShareRepository::class)]
#[Broadcast]
class QuizShare
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'quizShares')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quiz $quiz = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $start_date = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $finish_date = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $uuid = null;

    public function __construct()
    {
        $this->uuid = new UuidV7();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): static
    {
        $this->quiz = $quiz;

        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTimeImmutable $start_date): static
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getFinishDate(): ?\DateTimeImmutable
    {
        return $this->finish_date;
    }

    public function setFinishDate(?\DateTimeImmutable $finish_date): static
    {
        $this->finish_date = $finish_date;

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

    public function getQrCode(): string
    {
        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data("https://" . $_SERVER['SERVER_NAME'] . "/fr/quiz/redirect/" . $this->uuid)
            // ->data("paf://quiz/" . $this->uuid) // Fonctionne mais conflit avec certaines applications
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::Quartile)
            ->margin(10)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->logoPath(($this->isAvailable() ? 'assets/images/adrar_epa_logo_w_bg-5711b70db1d30a73d629b99c345bf989.png' : 'assets/images/lock-86937728444f01bc906bf2302329ffb4.png'))
            ->logoResizeToWidth(70)
            ->logoPunchoutBackground(false)
            ->labelText($this->quiz->getTitle())
            ->labelFont(new NotoSans(20))
            ->labelAlignment(LabelAlignment::Center)
            ->validateResult(false)
            ->build();
        return $result->getDataUri();
    }

    public function isAvailable(): bool
    {
        $now = new DateTimeImmutable();
        return (is_null($this->getStartDate()) || $this->getStartDate() <= $now) || (is_null($this->getFinishDate()) || $this->getFinishDate() >= $now);
    }
}
