<?php

namespace App\Test\Controller;

use App\Entity\Prospect;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProspectControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/prospect/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Prospect::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Prospect index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'prospect[name]' => 'Testing',
            'prospect[siren]' => 'Testing',
            'prospect[nic]' => 'Testing',
            'prospect[street]' => 'Testing',
            'prospect[postal_code]' => 'Testing',
            'prospect[city]' => 'Testing',
            'prospect[country]' => 'Testing',
            'prospect[email]' => 'Testing',
            'prospect[phone_number]' => 'Testing',
            'prospect[phone_number_bis]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Prospect();
        $fixture->setName('My Title');
        $fixture->setSiren('My Title');
        $fixture->setNic('My Title');
        $fixture->setStreet('My Title');
        $fixture->setPostal_code('My Title');
        $fixture->setCity('My Title');
        $fixture->setCountry('My Title');
        $fixture->setEmail('My Title');
        $fixture->setPhone_number('My Title');
        $fixture->setPhone_number_bis('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Prospect');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Prospect();
        $fixture->setName('Value');
        $fixture->setSiren('Value');
        $fixture->setNic('Value');
        $fixture->setStreet('Value');
        $fixture->setPostal_code('Value');
        $fixture->setCity('Value');
        $fixture->setCountry('Value');
        $fixture->setEmail('Value');
        $fixture->setPhone_number('Value');
        $fixture->setPhone_number_bis('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'prospect[name]' => 'Something New',
            'prospect[siren]' => 'Something New',
            'prospect[nic]' => 'Something New',
            'prospect[street]' => 'Something New',
            'prospect[postal_code]' => 'Something New',
            'prospect[city]' => 'Something New',
            'prospect[country]' => 'Something New',
            'prospect[email]' => 'Something New',
            'prospect[phone_number]' => 'Something New',
            'prospect[phone_number_bis]' => 'Something New',
        ]);

        self::assertResponseRedirects('/prospect/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getSiren());
        self::assertSame('Something New', $fixture[0]->getNic());
        self::assertSame('Something New', $fixture[0]->getStreet());
        self::assertSame('Something New', $fixture[0]->getPostal_code());
        self::assertSame('Something New', $fixture[0]->getCity());
        self::assertSame('Something New', $fixture[0]->getCountry());
        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getPhone_number());
        self::assertSame('Something New', $fixture[0]->getPhone_number_bis());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Prospect();
        $fixture->setName('Value');
        $fixture->setSiren('Value');
        $fixture->setNic('Value');
        $fixture->setStreet('Value');
        $fixture->setPostal_code('Value');
        $fixture->setCity('Value');
        $fixture->setCountry('Value');
        $fixture->setEmail('Value');
        $fixture->setPhone_number('Value');
        $fixture->setPhone_number_bis('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/prospect/');
        self::assertSame(0, $this->repository->count([]));
    }
}
