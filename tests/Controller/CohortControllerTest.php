<?php

namespace App\Test\Controller;

use App\Entity\Cohort;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CohortControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/cohort/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Cohort::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Cohort index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'cohort[name]' => 'Testing',
            'cohort[acronym]' => 'Testing',
            'cohort[shield]' => 'Testing',
            'cohort[documents]' => 'Testing',
            'cohort[startDate]' => 'Testing',
            'cohort[finishDate]' => 'Testing',
            'cohort[uuid]' => 'Testing',
            'cohort[trainer]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Cohort();
        $fixture->setName('My Title');
        $fixture->setAcronym('My Title');
        $fixture->setShield('My Title');
        $fixture->setDocuments('My Title');
        $fixture->setStartDate('My Title');
        $fixture->setFinishDate('My Title');
        $fixture->setUuid('My Title');
        $fixture->setTrainer('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Cohort');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Cohort();
        $fixture->setName('Value');
        $fixture->setAcronym('Value');
        $fixture->setShield('Value');
        $fixture->setDocuments('Value');
        $fixture->setStartDate('Value');
        $fixture->setFinishDate('Value');
        $fixture->setUuid('Value');
        $fixture->setTrainer('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'cohort[name]' => 'Something New',
            'cohort[acronym]' => 'Something New',
            'cohort[shield]' => 'Something New',
            'cohort[documents]' => 'Something New',
            'cohort[startDate]' => 'Something New',
            'cohort[finishDate]' => 'Something New',
            'cohort[uuid]' => 'Something New',
            'cohort[trainer]' => 'Something New',
        ]);

        self::assertResponseRedirects('/cohort/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getAcronym());
        self::assertSame('Something New', $fixture[0]->getShield());
        self::assertSame('Something New', $fixture[0]->getDocuments());
        self::assertSame('Something New', $fixture[0]->getStartDate());
        self::assertSame('Something New', $fixture[0]->getFinishDate());
        self::assertSame('Something New', $fixture[0]->getUuid());
        self::assertSame('Something New', $fixture[0]->getTrainer());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Cohort();
        $fixture->setName('Value');
        $fixture->setAcronym('Value');
        $fixture->setShield('Value');
        $fixture->setDocuments('Value');
        $fixture->setStartDate('Value');
        $fixture->setFinishDate('Value');
        $fixture->setUuid('Value');
        $fixture->setTrainer('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/cohort/');
        self::assertSame(0, $this->repository->count([]));
    }
}
