<?php

namespace App\Service;

class VersionService
{
    private string $version;

    public function __construct()
    {
        $composerFile = "../composer.json";
        if (file_exists($composerFile)) {
            $composerData = json_decode(file_get_contents($composerFile), true);
            $this->version = $composerData['version'] ?? 'N/A';
        } else {
            // Gérer l'erreur si le fichier n'existe pas
            $this->version = 'N/A';
        }
    }

    public function getVersion(): string
    {
        return $this->version;
    }
}
