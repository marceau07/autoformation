<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LocaleSwitcherExtension extends AbstractExtension
{
    public function __construct(private RequestStack $requestStack, private RouterInterface $router, private array $locales)
    {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('locale_switch_links', [$this, 'generateLocaleLinks'], ['is_safe' => ['html']]),
        ];
    }

    public function generateLocaleLinks(): array
    {
        $request = $this->requestStack->getCurrentRequest();
        $currentRoute = $request->attributes->get('_route');
        $currentParameters = $request->attributes->get('_route_params', []);
        $currentPath = $request->getPathInfo();
        $currentLocale = $request->getLocale();
        $query = $request->getQueryString();
        $queryPrefix = $query ? '?' . $query : '';

        $links = [];

        foreach ($this->locales as $locale => $label) {
            if ($locale === $currentLocale) {
                continue;
            }

            try {
                // Test si la route actuelle accepte un paramètre _locale
                $params = array_merge($currentParameters, ['_locale' => $locale]);
                $url = $this->router->generate($currentRoute, $params);
            } catch (\Exception) {
                // Fallback : insère /{locale}/ dans le path si possible
                $segments = explode('/', trim($currentPath, '/'));

                // Si le premier segment est une locale connue, on la remplace
                if (in_array($segments[0], array_keys($this->locales))) {
                    $segments[0] = $locale;
                } else {
                    array_unshift($segments, $locale);
                }

                $url = '/' . implode('/', $segments) . $queryPrefix;
            }

            $links[] = [
                'label' => $label,
                'url' => $url,
                'locale' => $locale,
            ];
        }

        return $links;
    }
}
