<?php

namespace App\Entity;

use App\Repository\SiteSettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteSettingsRepository::class)]
class SiteSettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $maintenance_mode = null;

    #[ORM\Column(length: 255)]
    private ?string $logo_path = null;

    #[ORM\Column(length: 75)]
    private ?string $logo_name = null;

    #[ORM\Column(length: 75)]
    private ?string $platform_name = null;

    #[ORM\Column(length: 7)]
    private ?string $primaryColor = null;

    #[ORM\Column(length: 7)]
    private ?string $secondaryColor = null;

    #[ORM\Column(length: 7)]
    private ?string $tertiaryColor = null;

    #[ORM\Column(length: 7)]
    private ?string $quaternaryColor = null;

    #[ORM\Column(length: 7)]
    private ?string $lightenColor = null;

    #[ORM\Column(length: 7)]
    private ?string $darkenColor = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isMaintenanceMode(): ?bool
    {
        return $this->maintenance_mode;
    }

    public function setMaintenanceMode(bool $maintenance_mode): static
    {
        $this->maintenance_mode = $maintenance_mode;

        return $this;
    }

    public function getLogoPath(): ?string
    {
        return $this->logo_path;
    }

    public function setLogoPath(string $logo_path): static
    {
        $this->logo_path = $logo_path;

        return $this;
    }

    public function getLogoName(): ?string
    {
        return $this->logo_name;
    }

    public function setLogoName(string $logo_name): static
    {
        $this->logo_name = $logo_name;

        return $this;
    }

    public function getPlatformName(): ?string
    {
        return $this->platform_name;
    }

    public function setPlatformName(string $platform_name): static
    {
        $this->platform_name = $platform_name;

        return $this;
    }

    public function getPrimaryColor(): ?string
    {
        return $this->primaryColor;
    }

    public function setPrimaryColor(string $primaryColor): static
    {
        $this->primaryColor = $primaryColor;

        return $this;
    }

    public function getSecondaryColor(): ?string
    {
        return $this->secondaryColor;
    }

    public function setSecondaryColor(string $secondaryColor): static
    {
        $this->secondaryColor = $secondaryColor;

        return $this;
    }

    public function getTertiaryColor(): ?string
    {
        return $this->tertiaryColor;
    }

    public function setTertiaryColor(string $tertiaryColor): static
    {
        $this->tertiaryColor = $tertiaryColor;

        return $this;
    }

    public function getQuaternaryColor(): ?string
    {
        return $this->quaternaryColor;
    }

    public function setQuaternaryColor(string $quaternaryColor): static
    {
        $this->quaternaryColor = $quaternaryColor;

        return $this;
    }

    public function getLightenColor(): ?string
    {
        return $this->lightenColor;
    }

    public function setLightenColor(string $lightenColor): static
    {
        $this->lightenColor = $lightenColor;

        return $this;
    }

    public function getDarkenColor(): ?string
    {
        return $this->darkenColor;
    }

    public function setDarkenColor(string $darkenColor): static
    {
        $this->darkenColor = $darkenColor;

        return $this;
    }
}
