<?php

namespace App\DataFixtures;

use App\Entity\Cohort;
use App\Entity\CohortInternship;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CohortInternshipFixtures extends Fixture implements DependentFixtureInterface
{
    public const COHORT_INTERNSHIP_REFERENCE_TAG = 'cohort-internship-';
    public const NB_COHORT_INTERNSHIP = 5;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::NB_COHORT_INTERNSHIP; $i++) {
            $cohortInternship = new CohortInternship();
            $cohortInternship->setLabel($faker->word());
            $cohortInternship->setStartDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 month', '+1 month')));
            $cohortInternship->setFinishDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('+8 month', '+10 month')));
            $cohortInternship->setDuration($faker->numberBetween(30, 270));
            $cohortInternship->setCohort($this->getReference(CohortFixtures::COHORT_REFERENCE_TAG . rand(0, CohortFixtures::NB_COHORT - 1), Cohort::class));
            $cohortInternship->setUuid($faker->uuid());

            $manager->persist($cohortInternship);
            $this->addReference(self::COHORT_INTERNSHIP_REFERENCE_TAG . $i, $cohortInternship);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CohortFixtures::class
        ];
    }
}
