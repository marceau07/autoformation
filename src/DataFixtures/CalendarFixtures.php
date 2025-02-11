<?php

namespace App\DataFixtures;

use App\Entity\Calendar;
use App\Entity\Cohort;
use App\Entity\Faq;
use App\Entity\Trainer;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CalendarFixtures extends Fixture implements DependentFixtureInterface
{
    public const CALENDAR_REFERENCE_TAG = 'Calendar-';
    public const NB_CALENDAR = 100;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::NB_CALENDAR; $i++) {
            $calendar = new Calendar();
            if ($faker->boolean(80)) {
                $calendar->setTrainer($this->getReference(TrainerFixtures::TRAINER_REFERENCE_TAG . rand(0, TrainerFixtures::NB_TRAINER - 1), Trainer::class));
                $calendar->setCohort(null);
            } else {
                $calendar->setTrainer(null);
                $calendar->setCohort($this->getReference(CohortFixtures::COHORT_REFERENCE_TAG . rand(0, CohortFixtures::NB_COHORT - 1), Cohort::class));
            }
            $calendar->setTitle($faker->title());
            $calendar->setDescription($faker->paragraphs(3, true));
            $calendar->setStartDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 month', '+1 month')));
            $calendar->setFinishDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 month', '+1 month')));
            $calendar->setUuid($faker->uuid());

            $manager->persist($calendar);
            $this->addReference(self::CALENDAR_REFERENCE_TAG . $i, $calendar);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CohortFixtures::class,
            TrainerFixtures::class
        ];
    }
}
