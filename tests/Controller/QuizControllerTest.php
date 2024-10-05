<?php

namespace App\Tests\Controller;

use App\Entity\Quiz;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class QuizControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/quiz/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Quiz::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Quiz index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'quiz[title]' => 'Testing',
            'quiz[uuid]' => 'Testing',
            'quiz[theme]' => 'Testing',
            'quiz[trainer]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Quiz();
        $fixture->setTitle('My Title');
        $fixture->setUuid('My Title');
        $fixture->setTheme('My Title');
        $fixture->setTrainer('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Quiz');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Quiz();
        $fixture->setTitle('Value');
        $fixture->setUuid('Value');
        $fixture->setTheme('Value');
        $fixture->setTrainer('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'quiz[title]' => 'Something New',
            'quiz[uuid]' => 'Something New',
            'quiz[theme]' => 'Something New',
            'quiz[trainer]' => 'Something New',
        ]);

        self::assertResponseRedirects('/quiz/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getUuid());
        self::assertSame('Something New', $fixture[0]->getTheme());
        self::assertSame('Something New', $fixture[0]->getTrainer());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Quiz();
        $fixture->setTitle('Value');
        $fixture->setUuid('Value');
        $fixture->setTheme('Value');
        $fixture->setTrainer('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/quiz/');
        self::assertSame(0, $this->repository->count([]));
    }
}
