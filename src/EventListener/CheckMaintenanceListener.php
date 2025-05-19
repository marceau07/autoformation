<?php

namespace App\EventListener;

use App\Entity\SiteSettings;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment as Twig;

final class CheckMaintenanceListener
{
    private $entityManager;
    private $twig;
    private $allowedIps;
    private $allowedRoutes;

    public function __construct(EntityManagerInterface $entityManager, Twig $twig)
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        // localhost, localhost IPV6, local ip, livebox, nordvpn
        $this->allowedIps = ['127.0.0.1', '::1', '192.168.1.33', '192.168.1.40', '192.168.1.254', '82.64.145.2']; // Liste des IP autorisées
        // $this->allowedIps = ['127.0.0.1', '192.168.1.33', '192.168.1.40', '192.168.1.254', '82.64.145.2', '138.199.16.217', '92.184.100.140']; // Liste des IP autorisées
        $this->allowedRoutes = [
            '/css/colors.css',
        ];
    }

    #[AsEventListener(event: KernelEvents::REQUEST)]
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $clientIp = $request->headers->get('X-Forwarded-For') ?? $request->getClientIp();
        $currentPath = $request->getPathInfo(); // <-- Récupère l'URL demandée (ex: /css/colors.css)

        // var_dump($currentPath);
        // var_dump($clientIp);
        // var_dump($this->allowedIps);
        // Vérifie si l'utilisateur est dans les IP autorisées
        if (in_array($clientIp, $this->allowedIps)) {
            return; // Autorisé
        }

        // Autoriser si la route est autorisée
        foreach ($this->allowedRoutes as $route) {
            if (str_starts_with($currentPath, $route)) {
                return;
            }
        }

        // Vérifie l'état de maintenance dans la base de données
        $settings = $this->entityManager->getRepository('App\Entity\SiteSettings')->find(1);
        if (!$settings) {
            $settings = new SiteSettings();
            $settings->setMaintenanceMode(true);
            $this->entityManager->persist($settings);
            $this->entityManager->flush();
        }
        if ($settings && $settings->isMaintenanceMode()) {
            $content = $this->twig->render('maintenance.html.twig');
            $response = new Response($content, Response::HTTP_SERVICE_UNAVAILABLE);
            $event->setResponse($response);
        }
    }
}
