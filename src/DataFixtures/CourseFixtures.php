<?php

namespace App\DataFixtures;

use App\Entity\Course;
use App\Entity\CourseModule;
use App\Entity\Trainer;
use App\Repository\CourseModuleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CourseFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        
        for($i = 0; $i < 10; $i++) {
            $trainer = new Trainer();
            $trainer->setUsername($faker->userName());
            $trainer->setPassword($faker->password());
            $trainer->setFirstname($faker->firstName());
            $trainer->setLastname($faker->lastName());
            $trainer->setEmail($faker->email());
            $trainer->setActivated($faker->boolean(75));
            $trainer->setRoles(["ROLE_TRAINER"]);
            $manager->persist($trainer);
            $this->setReference("trainer-" . $i, $trainer);
        }
        
        for ($i = 0; $i < 10; $i++) {
            $module = new CourseModule();
            $module->setLabel($faker->sentence(2));
            $module->setIllustration("not_found_404.svg");
            $module->setPosition($faker->numberBetween(1, 10));
            $module->setUuid($faker->uuid());
            $manager->persist($module);
            $this->setReference("module-" . $i, $module);
            for ($j = 0; $j < mt_rand(1, 13); $j++) {
                $course = new Course();
                $course->setTitle($faker->sentence(3));
                $course->setSynopsis($faker->paragraph(2));
                $course->setKeywords($faker->words(5, true));
                $course->setLink("2PACX-".$faker->uuid());
                $course->setPosition($faker->numberBetween(1, 10));
                $course->setModule($module);
                $course->setTrainer($this->getReference("trainer-" . mt_rand(0, 9)));
                $course->setVisitors(mt_rand(0, 1000));
                $manager->persist($course);
                $this->setReference("course-" . $j, $course);
            }
        }
        $manager->flush();
    }
}
