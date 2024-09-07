<?php

namespace App\DataFixtures;

use App\Entity\CourseCohort;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CourseCohortFixtures extends Fixture implements DependentFixtureInterface
{
    public const COURSE_COHORT_REFERENCE_TAG = 'course-cohort-';
    public const NB_COURSE_COHORT = 50;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        $usedCombinations = [];
        for ($i = 0; $i < self::NB_COURSE_COHORT; $i++) {
            do {
                $cohortIndex = rand(0, CohortFixtures::NB_COHORT - 1);
                $courseIndex = rand(0, CourseFixtures::NB_COURSE - 1);
                $combination = $cohortIndex . '|' . $courseIndex;
            } while (in_array($combination, $usedCombinations));
            $usedCombinations[] = $combination;

            $cc = new CourseCohort();
            $cc->setCohort($this->getReference(CohortFixtures::COHORT_REFERENCE_TAG . $cohortIndex));
            $cc->setCourse($this->getReference(CourseFixtures::COURSE_REFERENCE_TAG . $courseIndex));
            $cc->setActive($faker->boolean(70));
            
            $manager->persist($cc);
            $this->addReference(self::COURSE_COHORT_REFERENCE_TAG . $i, $cc);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CohortFixtures::class,
            CourseFixtures::class
        ];
    }
}
