<?php

namespace App\DataFixtures;

use App\Entity\QuizTheme;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class QuizThemeFixtures extends Fixture
{
    public const QUIZ_THEME_REFERENCE_TAG = 'quiz-theme-';
    public const NB_QUIZ_THEME = 5;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::NB_QUIZ_THEME; $i++) {
            $quizTheme = new QuizTheme();
            $quizTheme->setIllustration($faker->imageUrl(640, 480, 'abstract', true));
            $quizTheme->setColor($faker->hexColor());
            $quizTheme->setName($faker->sentence(3, true));

            $manager->persist($quizTheme);
            $this->addReference(self::QUIZ_THEME_REFERENCE_TAG . $i, $quizTheme);
        }
        $manager->flush();
    }
}
