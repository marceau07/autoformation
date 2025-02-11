<?php

namespace App\DataFixtures;

// use App\Entity\Quiz;
// use App\Entity\SurveyTrainee;
// use Doctrine\Bundle\FixturesBundle\Fixture;
// use Doctrine\Persistence\ObjectManager;
// use Faker\Factory;
// use Doctrine\Common\DataFixtures\DependentFixtureInterface;

// // The DependentFixtureInterface is imported to use the getDependencies method to avoid the error of loading fixtures in the wrong order
class QuizFixtures 
{
//     public const QUIZ_REFERENCE_TAG = 'quiz-';
//     public const NB_QUIZ = 50;

//     public function load(ObjectManager $manager): void
//     {
//         $faker = Factory::create('fr_FR');
        
//         for ($i = 0; $i < self::NB_QUIZ; $i++) {
//             $quiz = new Quiz();
//             $quiz->setTheme($this->getReference(QuizThemeFixtures::QUIZ_THEME_REFERENCE_TAG . rand(0, QuizThemeFixtures::NB_THEME - 1)));
//             $quiz->setTitle($faker->sentence(6, true));
//             $quiz->setUuid($faker->uuid());
//             $quiz->setTrainer($this->getReference(TrainerFixtures::TRAINER_REFERENCE_TAG . rand(0, TrainerFixtures::NB_TRAINER - 1)));
//             $quiz->setModule($this->getReference(CourseModuleFixtures::COURSE_MODULE_REFERENCE_TAG . rand(0, CourseModuleFixtures::NB_COURSE_MODULE - 1)));


//             $manager->persist($quiz);
//             $this->addReference(self::QUIZ_REFERENCE_TAG . $i, $quiz);
//         }
//         $manager->flush();
//     }

//     public function getDependencies()
//     {
//         return [
//             QuizThemeFixtures::class,
//             TrainerFixtures::class,
//             CourseModuleFixtures::class,
//         ];
//     }
}
