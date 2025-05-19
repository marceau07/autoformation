<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ChangelogController extends AbstractController
{
    #[Route('/{_locale}/changelogs', name: 'app_changelogs')]
    public function index(): Response
    {
        $url = "https://api.github.com/repos/marceau07/autoformation/releases?per_page=100";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
        $releases = curl_exec($curl);
        curl_close($curl);

        return $this->render('changelog/index.html.twig', [
            'releases' => $releases,
        ]);
    }

    #[Route('/{_locale}/changelog/{version}', name: 'app_changelog_version')]
    public function changelog(string $version): Response
    {
        $url = "https://api.github.com/repos/marceau07/autoformation/releases?per_page=100";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
        $releases = curl_exec($curl);
        curl_close($curl);

        return $this->render('changelog/index.html.twig', [
            'releases' => $releases,
            'version' => $version,
        ]);
    }
}
