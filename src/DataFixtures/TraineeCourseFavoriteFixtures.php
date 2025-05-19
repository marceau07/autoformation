<?php

namespace App\DataFixtures;

use App\Entity\Course;
use App\Entity\Trainee;
use App\Entity\TraineeCourseFavorite;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

// The DependentFixtureInterface is imported to use the getDependencies method to avoid the error of loading fixtures in the wrong order
class TraineeCourseFavoriteFixtures extends Fixture implements DependentFixtureInterface
{
    public const TRAINEE_COURSE_FAVORITE_REFERENCE_TAG = 'trainee-course-favorite-';
    public const NB_TRAINEE_COURSE_FAVORITE = 100;

    public function load(ObjectManager $manager): void
    {
        $usedCombinations = [];
        for ($i = 0; $i < self::NB_TRAINEE_COURSE_FAVORITE; $i++) {
            do {
                $traineeIndex = rand((ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR + TrainerFixtures::NB_TRAINER), (ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR + TrainerFixtures::NB_TRAINER + TraineeFixtures::NB_TRAINEE) - 1);
                $courseIndex = rand(0, CourseFixtures::NB_COURSE - 1);
                $combination = $traineeIndex . '|' . $courseIndex;
            } while (in_array($combination, $usedCombinations));
            $usedCombinations[] = $combination;
            
            $traineeCourseFavorite = new TraineeCourseFavorite();
            $traineeCourseFavorite->setTrainee($this->getReference(TraineeFixtures::TRAINEE_REFERENCE_TAG . $traineeIndex, Trainee::class));
            $traineeCourseFavorite->setCourse($this->getReference(CourseFixtures::COURSE_REFERENCE_TAG . $courseIndex, Course::class));

            $manager->persist($traineeCourseFavorite);
            $this->addReference(self::TRAINEE_COURSE_FAVORITE_REFERENCE_TAG . $i, $traineeCourseFavorite);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TraineeFixtures::class,
            CourseFixtures::class,
        ];
    }
}
