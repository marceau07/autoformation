<?php

namespace App\DataFixtures;

use App\Entity\CourseResource;
use App\Entity\Trainee;
use App\Entity\TraineeResource;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

// The DependentFixtureInterface is imported to use the getDependencies method to avoid the error of loading fixtures in the wrong order
class TraineeResourceFixtures extends Fixture implements DependentFixtureInterface
{
    public const TRAINEE_RESOURCE_REFERENCE_TAG = 'trainee-resource-';
    public const NB_TRAINEE_RESOURCE = 100;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $usedCombinations = [];
        for ($i = 0; $i < self::NB_TRAINEE_RESOURCE; $i++) {
            do {
                $traineeIndex = rand((ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR + TrainerFixtures::NB_TRAINER), (ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR + TrainerFixtures::NB_TRAINER + TraineeFixtures::NB_TRAINEE) - 1);
                $courseResourceIndex = rand(0, CourseResourceFixtures::NB_COURSE_RESOURCE - 1);
                $combination = $traineeIndex . '|' . $courseResourceIndex;
            } while (in_array($combination, $usedCombinations));
            $usedCombinations[] = $combination;

            $traineeResource = new TraineeResource();
            $traineeResource->setTrainee($this->getReference(TraineeFixtures::TRAINEE_REFERENCE_TAG . $traineeIndex, Trainee::class));
            $traineeResource->setCourseResource($this->getReference(CourseResourceFixtures::COURSE_RESOURCE_REFERENCE_TAG . $courseResourceIndex, CourseResource::class));
            $traineeResource->setLabel($faker->unique()->word() . '.' . $faker->fileExtension());

            $manager->persist($traineeResource);
            $this->addReference(self::TRAINEE_RESOURCE_REFERENCE_TAG . $i, $traineeResource);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TraineeFixtures::class,
            CourseResourceFixtures::class,
        ];
    }
}
