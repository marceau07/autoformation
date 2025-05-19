<?php 

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class LocaleService
{
    public function __construct(private RequestStack $requestStack, private RouterInterface $router, private array $locales)
    {}

    public function getLocaleSwitchUrls(): array
    {
        $request = $this->requestStack->getCurrentRequest();
        $currentRoute = $request->attributes->get('_route');
        $currentParams = $request->attributes->get('_route_params', []);
        $currentLocale = $request->getLocale();

        $urls = [];

        foreach ($this->locales as $locale => $label) {
            if ($locale !== $currentLocale) {
                try {
                    $params = [...$currentParams, '_locale' => $locale];
                    $urls[$locale] = [
                        'label' => $label,
                        'url' => $this->router->generate($currentRoute, $params)
                    ];
                } catch (\Exception) {
                    // fallback: return homepage in target locale
                    $urls[$locale] = [
                        'label' => $label,
                        'url' => $this->router->generate('admin', ['_locale' => $locale])
                    ];
                }
            }
        }

        return $urls;
    }
}
