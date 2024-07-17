<?php

namespace App\Test\Controller;

use App\Entity\Trainer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TrainerControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/trainer/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Trainer::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Trainer index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'trainer[username]' => 'Testing',
            'trainer[lastName]' => 'Testing',
            'trainer[firstName]' => 'Testing',
            'trainer[roles]' => 'Testing',
            'trainer[password]' => 'Testing',
            'trainer[email]' => 'Testing',
            'trainer[activated]' => 'Testing',
            'trainer[tmpCode]' => 'Testing',
            'trainer[tmpCodeDate]' => 'Testing',
            'trainer[signature]' => 'Testing',
            'trainer[uuid]' => 'Testing',
            'trainer[role]' => 'Testing',
            'trainer[phoneNumber]' => 'Testing',
            'trainer[entranceCode]' => 'Testing',
            'trainer[entranceCodeDate]' => 'Testing',
            'trainer[avatar]' => 'Testing',
            'trainer[sector]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Trainer();
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
        $fixture->setRole('My Title');
        $fixture->setPhoneNumber('My Title');
        $fixture->setEntranceCode('My Title');
        $fixture->setEntranceCodeDate('My Title');
        $fixture->setAvatar('My Title');
        $fixture->setSector('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Trainer');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Trainer();
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
        $fixture->setRole('Value');
        $fixture->setPhoneNumber('Value');
        $fixture->setEntranceCode('Value');
        $fixture->setEntranceCodeDate('Value');
        $fixture->setAvatar('Value');
        $fixture->setSector('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'trainer[username]' => 'Something New',
            'trainer[lastName]' => 'Something New',
            'trainer[firstName]' => 'Something New',
            'trainer[roles]' => 'Something New',
            'trainer[password]' => 'Something New',
            'trainer[email]' => 'Something New',
            'trainer[activated]' => 'Something New',
            'trainer[tmpCode]' => 'Something New',
            'trainer[tmpCodeDate]' => 'Something New',
            'trainer[signature]' => 'Something New',
            'trainer[uuid]' => 'Something New',
            'trainer[role]' => 'Something New',
            'trainer[phoneNumber]' => 'Something New',
            'trainer[entranceCode]' => 'Something New',
            'trainer[entranceCodeDate]' => 'Something New',
            'trainer[avatar]' => 'Something New',
            'trainer[sector]' => 'Something New',
        ]);

        self::assertResponseRedirects('/trainer/');

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
        self::assertSame('Something New', $fixture[0]->getRole());
        self::assertSame('Something New', $fixture[0]->getPhoneNumber());
        self::assertSame('Something New', $fixture[0]->getEntranceCode());
        self::assertSame('Something New', $fixture[0]->getEntranceCodeDate());
        self::assertSame('Something New', $fixture[0]->getAvatar());
        self::assertSame('Something New', $fixture[0]->getSector());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Trainer();
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
        $fixture->setRole('Value');
        $fixture->setPhoneNumber('Value');
        $fixture->setEntranceCode('Value');
        $fixture->setEntranceCodeDate('Value');
        $fixture->setAvatar('Value');
        $fixture->setSector('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/trainer/');
        self::assertSame(0, $this->repository->count([]));
    }
}
