<?php

namespace App\Test\Controller;

use App\Entity\Faq;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FaqControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/faq/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Faq::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Faq index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'faq[theme]' => 'Testing',
            'faq[title]' => 'Testing',
            'faq[content]' => 'Testing',
            'faq[visibility]' => 'Testing',
            'faq[priority]' => 'Testing',
            'faq[sector]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Faq();
        $fixture->setTheme('My Title');
        $fixture->setTitle('My Title');
        $fixture->setContent('My Title');
        $fixture->setVisibility('My Title');
        $fixture->setPriority('My Title');
        $fixture->setSector('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Faq');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Faq();
        $fixture->setTheme('Value');
        $fixture->setTitle('Value');
        $fixture->setContent('Value');
        $fixture->setVisibility('Value');
        $fixture->setPriority('Value');
        $fixture->setSector('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'faq[theme]' => 'Something New',
            'faq[title]' => 'Something New',
            'faq[content]' => 'Something New',
            'faq[visibility]' => 'Something New',
            'faq[priority]' => 'Something New',
            'faq[sector]' => 'Something New',
        ]);

        self::assertResponseRedirects('/faq/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTheme());
        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getContent());
        self::assertSame('Something New', $fixture[0]->getVisibility());
        self::assertSame('Something New', $fixture[0]->getPriority());
        self::assertSame('Something New', $fixture[0]->getSector());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Faq();
        $fixture->setTheme('Value');
        $fixture->setTitle('Value');
        $fixture->setContent('Value');
        $fixture->setVisibility('Value');
        $fixture->setPriority('Value');
        $fixture->setSector('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/faq/');
        self::assertSame(0, $this->repository->count([]));
    }
}
