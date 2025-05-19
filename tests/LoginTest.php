<?php 

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Panther\PantherTestCase;

class LoginTest extends PantherTestCase
{
    public function testLogin()
    {
        // Crée un client Panther (un navigateur web headless)
        $client = static::createPantherClient();

        // Va sur la page de connexion
        $crawler = $client->request('GET', '/login');

        // Sélectionne le formulaire de connexion
        $form = $crawler->selectButton('Se connecter')->form();

        // Renseigne les champs du formulaire
        $form['username'] = 'stagiaire';
        $form['password'] = 'mdp';

        // Soumets le formulaire
        $client->submit($form);

        // Attends que le chargement de la page soit terminé et vérifie la présence de texte
        $client->waitFor('.flash-notice');

        // Vérifie que la page contient le texte "Bienvenue" (ou un autre indicateur de succès)
        $this->assertSelectorTextContains('.flash-notice', 'Bienvenue');
    }
}
