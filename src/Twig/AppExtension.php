<?php

namespace App\Twig;

use App\Service\GlobalDataService;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    private $globalDataService;

    public function __construct(GlobalDataService $globalDataService)
    {
        $this->globalDataService = $globalDataService;
    }

    public function getGlobals(): array
    {
        return [
            'feedbackCategories' => $this->globalDataService->getFeedbackCategories(),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('json_decode', [$this, 'jsonDecode']),
        ];
    }

    /**
     * Decode a JSON string for use in a template file Twig
     * @param string $json
     * @return mixed
     */
    public function jsonDecode($json): mixed
    {
        return json_decode($json);
    }
}
