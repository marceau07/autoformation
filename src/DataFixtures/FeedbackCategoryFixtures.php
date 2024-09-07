<?php

namespace App\DataFixtures;

use App\Entity\FeedbackCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class FeedbackCategoryFixtures extends Fixture
{
    public const FEEDBACK_CATEGORY_REFERENCE_TAG = 'feedback-category-';
    public const NB_FEEDBACK_CATEGORY = 50;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::NB_FEEDBACK_CATEGORY; $i++) {
            $fc = new FeedbackCategory();
            $fc->setLabel($faker->randomElement(['Problème'], ['Suggestion'], ['Amélioration']));

            $manager->persist($fc);
            $this->addReference(self::FEEDBACK_CATEGORY_REFERENCE_TAG . $i, $fc);
        }

        $manager->flush();
    }
}
