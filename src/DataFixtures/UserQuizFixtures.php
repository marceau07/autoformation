<?php

namespace App\DataFixtures;

use App\Entity\Coordinator;
use App\Entity\QuizRow;
use App\Entity\Responsible;
use App\Entity\Trainee;
use App\Entity\Trainer;
use App\Entity\UserQuiz;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

// The DependentFixtureInterface is imported to use the getDependencies method to avoid the error of loading fixtures in the wrong order
class UserQuizFixtures extends Fixture implements DependentFixtureInterface
{
    public const USER_QUIZ_REFERENCE_TAG = 'user-quiz-';
    public const NB_USER_QUIZ = 5;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::NB_USER_QUIZ; $i++) {
            $userQuiz = new UserQuiz();
            $id = rand(0, (ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR + TrainerFixtures::NB_TRAINER + TraineeFixtures::NB_TRAINEE) - 1);
            $userQuiz->setUser(
                $this->getReference(TrainerFixtures::USER_REFERENCE_TAG . $id, ($id < ResponsibleFixtures::NB_RESPONSIBLE ? Responsible::class : 
                    ($id < (ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR) ? Coordinator::class : 
                    ($id < (ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR + TrainerFixtures::NB_TRAINER) ? Trainer::class : Trainee::class)))
                )
            );
            $userQuiz->setQuizRow($this->getReference(QuizRowFixtures::QUIZ_ROW_REFERENCE_TAG . rand(0, QuizRowFixtures::NB_QUIZ_ROW - 1), QuizRow::class));
            $userQuiz->setAnswer($faker->randomElement([
                $faker->randomNumber(),
                $faker->boolean(),
                $faker->sentence(3, true),
                $faker->word(),
            ]));
            $userQuiz->setFinishDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('now', '+10 minutes')));

            $manager->persist($userQuiz);
            $this->addReference(self::USER_QUIZ_REFERENCE_TAG . $i, $userQuiz);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            QuizRowFixtures::class,
            TraineeFixtures::class,
            TrainerFixtures::class,
            CoordinatorFixtures::class,
            ResponsibleFixtures::class,
        ];
    }
}
