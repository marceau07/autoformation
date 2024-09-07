<?php

namespace App\Test\Controller;

use App\Entity\Trainee;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TraineeControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/trainee/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Trainee::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Trainee index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'trainee[username]' => 'Testing',
            'trainee[lastName]' => 'Testing',
            'trainee[firstName]' => 'Testing',
            'trainee[roles]' => 'Testing',
            'trainee[password]' => 'Testing',
            'trainee[email]' => 'Testing',
            'trainee[activated]' => 'Testing',
            'trainee[tmpCode]' => 'Testing',
            'trainee[tmpCodeDate]' => 'Testing',
            'trainee[signature]' => 'Testing',
            'trainee[uuid]' => 'Testing',
            'trainee[passwordSave]' => 'Testing',
            'trainee[documents]' => 'Testing',
            'trainee[diploma]' => 'Testing',
            'trainee[avatar]' => 'Testing',
            'trainee[cohort]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Trainee();
        $fixture->setUsername('My Title');
        $fixture->setLastName('My Title');
        $fixture->setFirstName('My Title');
        $fixture->setRoles('My Title');
        $fixture->setPassword('My Title');
        $fixture->setEmail('My Title');
        $fixture->setActivated('My Title');
        $fixture->setTmpCode('My Title');
        $fixture->setTmpCodeDate('My Title');
        $fixture->setSignature('My Title');
        $fixture->setUuid('My Title');
        $fixture->setPasswordSave('My Title');
        $fixture->setDocuments('My Title');
        $fixture->setDiploma('My Title');
        $fixture->setAvatar('My Title');
        $fixture->setCohort('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Trainee');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Trainee();
        $fixture->setUsername('Value');
        $fixture->setLastName('Value');
        $fixture->setFirstName('Value');
        $fixture->setRoles('Value');
        $fixture->setPassword('Value');
        $fixture->setEmail('Value');
        $fixture->setActivated('Value');
        $fixture->setTmpCode('Value');
        $fixture->setTmpCodeDate('Value');
        $fixture->setSignature('Value');
        $fixture->setUuid('Value');
        $fixture->setPasswordSave('Value');
        $fixture->setDocuments('Value');
        $fixture->setDiploma('Value');
        $fixture->setAvatar('Value');
        $fixture->setCohort('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'trainee[username]' => 'Something New',
            'trainee[lastName]' => 'Something New',
            'trainee[firstName]' => 'Something New',
            'trainee[roles]' => 'Something New',
            'trainee[password]' => 'Something New',
            'trainee[email]' => 'Something New',
            'trainee[activated]' => 'Something New',
            'trainee[tmpCode]' => 'Something New',
            'trainee[tmpCodeDate]' => 'Something New',
            'trainee[signature]' => 'Something New',
            'trainee[uuid]' => 'Something New',
            'trainee[passwordSave]' => 'Something New',
            'trainee[documents]' => 'Something New',
            'trainee[diploma]' => 'Something New',
            'trainee[avatar]' => 'Something New',
            'trainee[cohort]' => 'Something New',
        ]);

        self::assertResponseRedirects('/trainee/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getUsername());
        self::assertSame('Something New', $fixture[0]->getLastName());
        self::assertSame('Something New', $fixture[0]->getFirstName());
        self::assertSame('Something New', $fixture[0]->getRoles());
        self::assertSame('Something New', $fixture[0]->getPassword());
        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getActivated());
        self::assertSame('Something New', $fixture[0]->getTmpCode());
        self::assertSame('Something New', $fixture[0]->getTmpCodeDate());
        self::assertSame('Something New', $fixture[0]->getSignature());
        self::assertSame('Something New', $fixture[0]->getUuid());
        self::assertSame('Something New', $fixture[0]->getPasswordSave());
        self::assertSame('Something New', $fixture[0]->getDocuments());
        self::assertSame('Something New', $fixture[0]->getDiploma());
        self::assertSame('Something New', $fixture[0]->getAvatar());
        self::assertSame('Something New', $fixture[0]->getCohort());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Trainee();
        $fixture->setUsername('Value');
        $fixture->setLastName('Value');
        $fixture->setFirstName('Value');
        $fixture->setRoles('Value');
        $fixture->setPassword('Value');
        $fixture->setEmail('Value');
        $fixture->setActivated('Value');
        $fixture->setTmpCode('Value');
        $fixture->setTmpCodeDate('Value');
        $fixture->setSignature('Value');
        $fixture->setUuid('Value');
        $fixture->setPasswordSave('Value');
        $fixture->setDocuments('Value');
        $fixture->setDiploma('Value');
        $fixture->setAvatar('Value');
        $fixture->setCohort('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/trainee/');
        self::assertSame(0, $this->repository->count([]));
    }
}
