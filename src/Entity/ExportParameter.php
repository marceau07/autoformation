<?php

namespace App\Entity;

use App\Repository\ExportParameterRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Cache(usage: "READ_ONLY", region: "read_only")]
#[ORM\Entity(repositoryClass: ExportParameterRepository::class)]
class ExportParameter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $dtype;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $field = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDtype(): ?string
    {
        return $this->dtype;
    }

    public function setDtype(?string $dtype): static
    {
        $this->dtype = $dtype;

        return $this;
    }

    public function getField(): ?string
    {
        return $this->field;
    }

    public function setField(string $field): static
    {
        $this->field = $field;

        return $this;
    }
}
