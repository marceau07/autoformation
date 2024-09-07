<?php

namespace App\DataFixtures;

use App\Entity\Cohort;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CohortFixtures extends Fixture implements DependentFixtureInterface
{
    public const COHORT_REFERENCE_TAG = 'cohort-';
    public const NB_COHORT = 5;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::NB_COHORT; $i++) {
            $cohort = new Cohort();
            $cohort->setTrainer($this->getReference(TrainerFixtures::TRAINER_REFERENCE_TAG . rand(0, TrainerFixtures::NB_TRAINER - 1)));
            $cohort->setName($faker->word());
            $cohort->setAcronym($faker->word());
            $cohort->setShield($faker->imageUrl(640, 480, 'shield'));
            $cohort->setDocuments('{}');
            $cohort->setStartDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 month', '+1 month')));
            $cohort->setFinishDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('+8 month', '+10 month')));
            $cohort->setUuid($faker->uuid());

            $manager->persist($cohort);
            $this->addReference(self::COHORT_REFERENCE_TAG . $i, $cohort);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TrainerFixtures::class
        ];
    }
}
