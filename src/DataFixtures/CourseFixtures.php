<?php

namespace App\DataFixtures;

use App\Entity\Course;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CourseFixtures extends Fixture implements DependentFixtureInterface
{
    public const COURSE_REFERENCE_TAG = 'course-';
    public const NB_COURSE = 200;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::NB_COURSE; $i++) {
            $course = new Course();
            $course->setTitle($faker->title());
            $course->setSynopsis($faker->paragraph());
            // Generate a random number of keywords separated by commas with $faker
            $keywords = $faker->words(rand(1, 50));
            $course->setKeywords(implode(';', $keywords));
            $course->setLink("2PACK-" . $faker->randomLetter() . $faker->randomLetter() . $faker->randomLetter() . $faker->randomLetter() . $faker->randomLetter());
            $course->setPosition(rand(0, self::NB_COURSE));
            $course->setModule($this->getReference(CourseModuleFixtures::COURSE_MODULE_REFERENCE_TAG . rand(0, CourseModuleFixtures::NB_COURSE_MODULE - 1)));
            $course->setTrainer($this->getReference(TrainerFixtures::TRAINER_REFERENCE_TAG . rand(0, TrainerFixtures::NB_TRAINER - 1)));
            $course->setVisitors(rand(0, 10000));

            $manager->persist($course);
            $this->addReference(self::COURSE_REFERENCE_TAG . $i, $course);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CourseModuleFixtures::class,
            TrainerFixtures::class
        ];
    }
}
