<?php

namespace App\DataFixtures;

use App\Entity\CourseModule;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CourseModuleFixtures extends Fixture
{
    public const COURSE_MODULE_REFERENCE_TAG = 'course-module-';
    public const NB_COURSE_MODULE = 100;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::NB_COURSE_MODULE; $i++) {
            $cm = new CourseModule();
            $cm->setLabel($faker->countryCode() . ' - ' . $faker->word());
            $cm->setPosition(rand(0, self::NB_COURSE_MODULE));
            $cm->setUuid($faker->uuid());
            $cm->setIllustration($faker->imageUrl(300, 300, 'illustration', true, 'Faker', false, 'png'));

            $manager->persist($cm);
            $this->addReference(self::COURSE_MODULE_REFERENCE_TAG . $i, $cm);
        }

        $manager->flush();
    }
}
