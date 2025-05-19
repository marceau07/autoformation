<?php

namespace App\DataFixtures;

use App\Config\EventType;
use App\Entity\Calendar;
use App\Entity\Cohort;
use App\Entity\Coordinator;
use App\Entity\Responsible;
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
                $id = rand(0, (ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR + TrainerFixtures::NB_TRAINER) - 1);
                $calendar->setUser(
                    $this->getReference(TrainerFixtures::USER_REFERENCE_TAG . $id, ($id < ResponsibleFixtures::NB_RESPONSIBLE ? Responsible::class : 
                        ($id < (ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR) ? Coordinator::class : Trainer::class))
                    )
                );
                $calendar->setCohort(null);
            } else {
                $calendar->setUser(null);
                $calendar->setCohort($this->getReference(CohortFixtures::COHORT_REFERENCE_TAG . rand(0, CohortFixtures::NB_COHORT - 1), Cohort::class));
            }
            $calendar->setTitle($faker->title());
            $calendar->setDescription($faker->paragraphs(3, true));
            $calendar->setStartDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 month', '+1 month')));
            $calendar->setFinishDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 month', '+1 month')));
            $calendar->setUuid($faker->uuid());
            $calendar->setEventType($faker->randomElement([EventType::WORK_STOPAGE, EventType::VACATION, EventType::ASYNCHRONOUS_MODULE, EventType::SYNCHRONOUS_MODULE]));

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
