<?php

namespace App\Tests\Controller;

use App\Entity\QuizRow;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class QuizRowControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/quiz/row/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(QuizRow::class);

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
        self::assertPageTitleContains('QuizRow index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'quiz_row[uuid]' => 'Testing',
            'quiz_row[question]' => 'Testing',
            'quiz_row[answer1]' => 'Testing',
            'quiz_row[answer2]' => 'Testing',
            'quiz_row[answer3]' => 'Testing',
            'quiz_row[answer4]' => 'Testing',
            'quiz_row[answer_short_text]' => 'Testing',
            'quiz_row[answer_long_text]' => 'Testing',
            'quiz_row[quiz_type]' => 'Testing',
            'quiz_row[timer]' => 'Testing',
            'quiz_row[score]' => 'Testing',
            'quiz_row[hint]' => 'Testing',
            'quiz_row[answer_explanation]' => 'Testing',
            'quiz_row[quiz]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new QuizRow();
        $fixture->setUuid('My Title');
        $fixture->setQuestion('My Title');
        $fixture->setAnswer1('My Title');
        $fixture->setAnswer2('My Title');
        $fixture->setAnswer3('My Title');
        $fixture->setAnswer4('My Title');
        $fixture->setAnswer_short_text('My Title');
        $fixture->setAnswer_long_text('My Title');
        $fixture->setQuiz_type('My Title');
        $fixture->setTimer('My Title');
        $fixture->setScore('My Title');
        $fixture->setHint('My Title');
        $fixture->setAnswer_explanation('My Title');
        $fixture->setQuiz('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('QuizRow');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new QuizRow();
        $fixture->setUuid('Value');
        $fixture->setQuestion('Value');
        $fixture->setAnswer1('Value');
        $fixture->setAnswer2('Value');
        $fixture->setAnswer3('Value');
        $fixture->setAnswer4('Value');
        $fixture->setAnswer_short_text('Value');
        $fixture->setAnswer_long_text('Value');
        $fixture->setQuiz_type('Value');
        $fixture->setTimer('Value');
        $fixture->setScore('Value');
        $fixture->setHint('Value');
        $fixture->setAnswer_explanation('Value');
        $fixture->setQuiz('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'quiz_row[uuid]' => 'Something New',
            'quiz_row[question]' => 'Something New',
            'quiz_row[answer1]' => 'Something New',
            'quiz_row[answer2]' => 'Something New',
            'quiz_row[answer3]' => 'Something New',
            'quiz_row[answer4]' => 'Something New',
            'quiz_row[answer_short_text]' => 'Something New',
            'quiz_row[answer_long_text]' => 'Something New',
            'quiz_row[quiz_type]' => 'Something New',
            'quiz_row[timer]' => 'Something New',
            'quiz_row[score]' => 'Something New',
            'quiz_row[hint]' => 'Something New',
            'quiz_row[answer_explanation]' => 'Something New',
            'quiz_row[quiz]' => 'Something New',
        ]);

        self::assertResponseRedirects('/quiz/row/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getUuid());
        self::assertSame('Something New', $fixture[0]->getQuestion());
        self::assertSame('Something New', $fixture[0]->getAnswer1());
        self::assertSame('Something New', $fixture[0]->getAnswer2());
        self::assertSame('Something New', $fixture[0]->getAnswer3());
        self::assertSame('Something New', $fixture[0]->getAnswer4());
        self::assertSame('Something New', $fixture[0]->getAnswer_short_text());
        self::assertSame('Something New', $fixture[0]->getAnswer_long_text());
        self::assertSame('Something New', $fixture[0]->getQuiz_type());
        self::assertSame('Something New', $fixture[0]->getTimer());
        self::assertSame('Something New', $fixture[0]->getScore());
        self::assertSame('Something New', $fixture[0]->getHint());
        self::assertSame('Something New', $fixture[0]->getAnswer_explanation());
        self::assertSame('Something New', $fixture[0]->getQuiz());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new QuizRow();
        $fixture->setUuid('Value');
        $fixture->setQuestion('Value');
        $fixture->setAnswer1('Value');
        $fixture->setAnswer2('Value');
        $fixture->setAnswer3('Value');
        $fixture->setAnswer4('Value');
        $fixture->setAnswer_short_text('Value');
        $fixture->setAnswer_long_text('Value');
        $fixture->setQuiz_type('Value');
        $fixture->setTimer('Value');
        $fixture->setScore('Value');
        $fixture->setHint('Value');
        $fixture->setAnswer_explanation('Value');
        $fixture->setQuiz('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/quiz/row/');
        self::assertSame(0, $this->repository->count([]));
    }
}
