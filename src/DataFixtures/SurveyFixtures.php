<?php

namespace App\DataFixtures;

use App\Entity\Survey;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SurveyFixtures extends Fixture
{
    public const SURVEY_REFERENCE_TAG = 'survey-';
    public const NB_SURVEY = 10;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::NB_SURVEY; $i++) {
            $survey = new Survey();
            $survey->setQuestion($faker->unique()->text(100));
            $survey->setResume($faker->text(50));

            $manager->persist($survey);
            $this->addReference(self::SURVEY_REFERENCE_TAG . $i, $survey);
        }

        $manager->flush();
    }
}
