<?php

namespace App\DataFixtures;

use App\Entity\SurveyTrainee;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

// The DependentFixtureInterface is imported to use the getDependencies method to avoid the error of loading fixtures in the wrong order
class SurveyTraineeFixtures extends Fixture implements DependentFixtureInterface
{
    public const SURVEY_TRAINEE_REFERENCE_TAG = 'survey-trainee-';
    public const NB_SURVEY_TRAINEE = 50;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        $usedCombinations = [];
        for ($i = 0; $i < self::NB_SURVEY_TRAINEE; $i++) {
            do {
                $surveyIndex = rand(0, SurveyFixtures::NB_SURVEY - 1);
                $traineeIndex = rand(TrainerFixtures::NB_TRAINER, (TrainerFixtures::NB_TRAINER + TraineeFixtures::NB_TRAINEE - 1));
                $combination = $surveyIndex . '|' . $traineeIndex;
            } while (in_array($combination, $usedCombinations));
            $usedCombinations[] = $combination;

            $st = new SurveyTrainee();
            $st->setSurvey($this->getReference(SurveyFixtures::SURVEY_REFERENCE_TAG . $surveyIndex));
            $st->setTrainee($this->getReference(TraineeFixtures::TRAINEE_REFERENCE_TAG . $traineeIndex));
            $st->setRate(rand(0, 5));
            $st->setAnswer($faker->text(250));

            $manager->persist($st);
            $this->addReference(self::SURVEY_TRAINEE_REFERENCE_TAG . $i, $st);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            SurveyFixtures::class,
            TraineeFixtures::class
        ];
    }
}
