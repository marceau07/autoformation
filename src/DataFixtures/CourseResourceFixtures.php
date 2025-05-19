<?php

namespace App\DataFixtures;

use App\Config\CourseResourceType;
use App\Entity\Course;
use App\Entity\CourseModule;
use App\Entity\CourseResource;
use App\Entity\Trainer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CourseResourceFixtures extends Fixture implements DependentFixtureInterface
{
    public const COURSE_RESOURCE_REFERENCE_TAG = 'course-resource-';
    public const NB_COURSE_RESOURCE = 30;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::NB_COURSE_RESOURCE; $i++) {
            $courseResource = new CourseResource();
            $courseResource->setCourse($this->getReference(CourseFixtures::COURSE_REFERENCE_TAG . rand(0, CourseFixtures::NB_COURSE - 1), Course::class));
            $courseResource->setTitle($faker->title());
            $courseResource->setResume($faker->paragraph());
            $courseResource->setLink($faker->url());
            $courseResource->setType($faker->randomElement([CourseResourceType::OTHER->value, CourseResourceType::EXERCISE->value, CourseResourceType::TP->value]));
            if ($faker->boolean(20)) {
                $courseResource->setRecord($faker->uuid() . "." . $faker->fileExtension());
                $courseResource->setRecordLink($faker->uuid());
            } else {
                $courseResource->setRecord(null);
                $courseResource->setRecordLink(null);
            }

            $manager->persist($courseResource);
            $this->addReference(self::COURSE_RESOURCE_REFERENCE_TAG . $i, $courseResource);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CourseFixtures::class,
        ];
    }
}
