<?php

namespace App\Twig;

use App\Service\GlobalDataService;
use App\Service\VersionService;
use DateTimeImmutable;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    private $globalDataService;
    private $security;
    private $translator;
    private VersionService $versionService;

    public function __construct(GlobalDataService $globalDataService, Security $security, TranslatorInterface $translator, VersionService $versionService)
    {
        $this->globalDataService = $globalDataService;
        $this->security = $security;
        $this->translator = $translator;
        $this->versionService = $versionService;
    }

    public function getGlobals(): array
    {
        return [
            'feedbackCategories' => $this->globalDataService->getFeedbackCategories(),
            'notificationsHomeworksToDo' => $this->globalDataService->getNotificationsHomeworksToDo($this->getCurrentUser()),
            'notificationsNews' => $this->globalDataService->getNotificationsNews(),
            'notificationsMessages' => $this->globalDataService->getNotificationsMessages($this->getCurrentUser()),
            'notificationsInternships' => $this->globalDataService->getNotificationsInternships($this->getCurrentUser()),
            'notificationsNewCourses' => $this->globalDataService->getNotificationsNewCourses($this->getCurrentUser()),
            'platformName' => $this->globalDataService->getPlatformName(),
            'platformLogoName' => $this->globalDataService->getPlatformLogoName(),
            'platformLogoPath' => $this->globalDataService->getPlatformLogoPath(),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('json_decode', [$this, 'jsonDecode']),
            new TwigFilter('time_ago', [$this, 'timeAgo']),
            new TwigFilter('ondisk', [$this, 'onDisk']),
            new TwigFilter('linkify', [$this, 'linkifyText'], ['is_safe' => ['html']]),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('app_version', [$this, 'getVersion']),
        ];
    }

    public function getVersion(): string
    {
        return $this->versionService->getVersion();
    }

    public function getCurrentUser()
    {
        return $this->security->getUser();
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

    function timeAgo($datetime, $full = false)
    {
        $now = new DateTimeImmutable();
        // Ensure $datetime is a DateTime object
        if (!$datetime instanceof DateTimeImmutable) {
            $datetime = new DateTimeImmutable($datetime);
        }
        // Calculate the difference
        $diff = $now->diff($datetime);

        // Define the time periods
        $units = [
            'y' => 'year',
            'm' => 'month',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        ];

        // Calculate the time difference in each unit
        $diffStringValue = [];
        $diffStringText = [];
        foreach ($units as $key => $value) {
            if ($diff->$key) {
                $diffStringValue[] = $diff->$key;
                $diffStringText[] = $value . ($diff->$key > 1 ? 's' : '');
            }
        }

        // Join the time units with appropriate punctuation
        if (!$full) {
            $diffStringValue = array_slice($diffStringValue, 0, 1);
            $diffStringText = array_slice($diffStringText, 0, 1);
        }


        if ($diffStringValue && $diffStringText) {
            return $this->translator->trans('global.' . implode(', ', $diffStringText), ['%time%' => implode(', ', $diffStringValue)], "messages");
        } else {
            return $this->translator->trans('global.now');
        }
        // return $diffString ? implode(', ', $diffString) . ' ago' : 'just now';
    }

    /**
     * Check if a file exists on disk
     * @param string $path
     * @return bool
     */
    public function onDisk($path)
    {
        return file_exists($path);
    }

    /**
     * Convert URLs in a string to clickable links
     * @param string $text
     * @return string
     */
    public function linkifyText(string $text): string
    {
        $pattern = '/(https?:\/\/[^\s]+)/i';
        $replacement = '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>';
        return preg_replace($pattern, $replacement, $text);
    }
}
