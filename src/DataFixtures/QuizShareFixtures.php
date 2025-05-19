<?php

namespace App\DataFixtures;

use App\Entity\Quiz;
use App\Entity\QuizShare;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

// The DependentFixtureInterface is imported to use the getDependencies method to avoid the error of loading fixtures in the wrong order
class QuizShareFixtures extends Fixture implements DependentFixtureInterface
{
    public const QUIZ_SHARE_REFERENCE_TAG = 'quiz-share-';
    public const NB_QUIZ_SHARE = 5;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::NB_QUIZ_SHARE; $i++) {
            $quizShare = new QuizShare();
            $quizShare->setQuiz($this->getReference(QuizFixtures::QUIZ_REFERENCE_TAG . rand(0, QuizFixtures::NB_QUIZ - 1), Quiz::class));
            $quizShare->setStartDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 month', 'now')));
            $quizShare->setFinishDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('+1 day', '+1 week')));
            $quizShare->setUuid($faker->uuid());

            $manager->persist($quizShare);
            $this->addReference(self::QUIZ_SHARE_REFERENCE_TAG . $i, $quizShare);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            QuizFixtures::class,
        ];
    }
}
