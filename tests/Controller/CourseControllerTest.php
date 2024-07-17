<?php

namespace App\Test\Controller;

use App\Entity\Course;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CourseControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/course/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Course::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Course index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'course[title]' => 'Testing',
            'course[synopsis]' => 'Testing',
            'course[keywords]' => 'Testing',
            'course[link]' => 'Testing',
            'course[position]' => 'Testing',
            'course[visitors]' => 'Testing',
            'course[module]' => 'Testing',
            'course[trainer]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Course();
        $fixture->setTitle('My Title');
        $fixture->setSynopsis('My Title');
        $fixture->setKeywords('My Title');
        $fixture->setLink('My Title');
        $fixture->setPosition('My Title');
        $fixture->setVisitors('My Title');
        $fixture->setModule('My Title');
        $fixture->setTrainer('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Course');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Course();
        $fixture->setTitle('Value');
        $fixture->setSynopsis('Value');
        $fixture->setKeywords('Value');
        $fixture->setLink('Value');
        $fixture->setPosition('Value');
        $fixture->setVisitors('Value');
        $fixture->setModule('Value');
        $fixture->setTrainer('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'course[title]' => 'Something New',
            'course[synopsis]' => 'Something New',
            'course[keywords]' => 'Something New',
            'course[link]' => 'Something New',
            'course[position]' => 'Something New',
            'course[visitors]' => 'Something New',
            'course[module]' => 'Something New',
            'course[trainer]' => 'Something New',
        ]);

        self::assertResponseRedirects('/course/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getSynopsis());
        self::assertSame('Something New', $fixture[0]->getKeywords());
        self::assertSame('Something New', $fixture[0]->getLink());
        self::assertSame('Something New', $fixture[0]->getPosition());
        self::assertSame('Something New', $fixture[0]->getVisitors());
        self::assertSame('Something New', $fixture[0]->getModule());
        self::assertSame('Something New', $fixture[0]->getTrainer());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Course();
        $fixture->setTitle('Value');
        $fixture->setSynopsis('Value');
        $fixture->setKeywords('Value');
        $fixture->setLink('Value');
        $fixture->setPosition('Value');
        $fixture->setVisitors('Value');
        $fixture->setModule('Value');
        $fixture->setTrainer('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/course/');
        self::assertSame(0, $this->repository->count([]));
    }
}
