<?php

namespace App\DataFixtures;

use App\Config\QuizType;
use App\Entity\CourseModule;
use App\Entity\Quiz;
use App\Entity\QuizRow;
use App\Entity\Trainer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

// The DependentFixtureInterface is imported to use the getDependencies method to avoid the error of loading fixtures in the wrong order
class QuizRowFixtures extends Fixture implements DependentFixtureInterface
{
    public const QUIZ_ROW_REFERENCE_TAG = 'quiz-row-';
    public const NB_QUIZ_ROW = 50;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::NB_QUIZ_ROW; $i++) {
            $quizRow = new QuizRow();
            $quizRow->setQuiz($this->getReference(QuizFixtures::QUIZ_REFERENCE_TAG . rand(0, QuizFixtures::NB_QUIZ - 1), Quiz::class));
            $quizRow->setUuid($faker->uuid());
            $quizRow->setQuestion($faker->sentence(6, true));
            $quizRow->setQuizType($faker->randomElement([
                QuizType::MULTIPLE_CHOICE,
                QuizType::TRUE_FALSE,
                QuizType::LONG_ANSWER,
                QuizType::SHORT_ANSWER,
                QuizType::UNIQUE_CHOICE,
            ]));
            $quizRow->setTimer($faker->randomElement([0, 30, 60, 120, 300]));
            $quizRow->setScore($faker->randomElement([0, 1, 2, 3, 4, 5]));
            $quizRow->setHint($faker->sentence(6, true));
            $quizRow->setAnswerExplanation($faker->sentence(6, true));
            if ($quizRow->getQuizType() === QuizType::MULTIPLE_CHOICE) {
                $quizRow->setOption1($faker->sentence(3, true));
                $quizRow->setOption2($faker->sentence(3, true));
                $quizRow->setOption3($faker->sentence(3, true));
                $quizRow->setOption4($faker->sentence(3, true));
                $quizRow->setAnswer(rand(1, 4) . ', ' . rand(1, 4));
            } elseif ($quizRow->getQuizType() === QuizType::UNIQUE_CHOICE) {
                $quizRow->setOption1($faker->sentence(3, true));
                $quizRow->setOption2($faker->sentence(3, true));
                $quizRow->setOption3($faker->sentence(3, true));
                $quizRow->setOption4($faker->sentence(3, true));
                $quizRow->setAnswer(rand(1, 4));
            } elseif ($quizRow->getQuizType() === QuizType::LONG_ANSWER) {
                $quizRow->setAnswer(implode(' ', $faker->words(10)));
            } elseif ($quizRow->getQuizType() === QuizType::SHORT_ANSWER) {
                $quizRow->setAnswer($faker->word());
            } elseif ($quizRow->getQuizType() === QuizType::TRUE_FALSE) {
                $quizRow->setAnswer($faker->randomElement([true, false]));
            }

            $manager->persist($quizRow);
            $this->addReference(self::QUIZ_ROW_REFERENCE_TAG . $i, $quizRow);
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
