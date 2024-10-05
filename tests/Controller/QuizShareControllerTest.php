<?php

namespace App\Tests\Controller;

use App\Entity\QuizShare;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class QuizShareControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/quiz/share/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(QuizShare::class);

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
        self::assertPageTitleContains('QuizShare index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'quiz_share[start_date]' => 'Testing',
            'quiz_share[finish_date]' => 'Testing',
            'quiz_share[quiz]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new QuizShare();
        $fixture->setStart_date('My Title');
        $fixture->setFinish_date('My Title');
        $fixture->setQuiz('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('QuizShare');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new QuizShare();
        $fixture->setStart_date('Value');
        $fixture->setFinish_date('Value');
        $fixture->setQuiz('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'quiz_share[start_date]' => 'Something New',
            'quiz_share[finish_date]' => 'Something New',
            'quiz_share[quiz]' => 'Something New',
        ]);

        self::assertResponseRedirects('/quiz/share/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getStart_date());
        self::assertSame('Something New', $fixture[0]->getFinish_date());
        self::assertSame('Something New', $fixture[0]->getQuiz());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new QuizShare();
        $fixture->setStart_date('Value');
        $fixture->setFinish_date('Value');
        $fixture->setQuiz('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/quiz/share/');
        self::assertSame(0, $this->repository->count([]));
    }
}
