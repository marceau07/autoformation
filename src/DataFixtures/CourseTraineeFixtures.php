<?php

namespace App\DataFixtures;

use App\Entity\Course;
use App\Entity\CourseTrainee;
use App\Entity\Trainee;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CourseTraineeFixtures extends Fixture implements DependentFixtureInterface
{
    public const COURSE_TRAINEE_REFERENCE_TAG = 'course-trainee-';
    public const NB_COURSE_TRAINEE = 200;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $usedCombinations = [];
        for ($i = 0; $i < self::NB_COURSE_TRAINEE; $i++) {
            do {
                $courseIndex = rand(0, CourseFixtures::NB_COURSE - 1);
                $traineeIndex = rand((ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR + TrainerFixtures::NB_TRAINER), (ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR + TrainerFixtures::NB_TRAINER + TraineeFixtures::NB_TRAINEE) - 1);
                $combination = $courseIndex . '|' . $traineeIndex;
            } while (in_array($combination, $usedCombinations));
            $usedCombinations[] = $combination;
            
            $courseTrainee = new CourseTrainee();
            $courseTrainee->setCourse($this->getReference(CourseFixtures::COURSE_REFERENCE_TAG . $courseIndex, Course::class));
            $courseTrainee->setTrainee($this->getReference(TraineeFixtures::TRAINEE_REFERENCE_TAG . $traineeIndex, Trainee::class));
            $courseTrainee->setDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', '-1 month')));

            $manager->persist($courseTrainee);
            $this->addReference(self::COURSE_TRAINEE_REFERENCE_TAG . $i, $courseTrainee);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CourseFixtures::class,
            TraineeFixtures::class,
        ];
    }
}
