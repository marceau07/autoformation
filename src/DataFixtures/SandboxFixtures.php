<?php

namespace App\DataFixtures;

use App\Entity\Course;
use App\Entity\Sandbox;
use App\Entity\Trainer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

// The DependentFixtureInterface is imported to use the getDependencies method to avoid the error of loading fixtures in the wrong order
class SandboxFixtures extends Fixture implements DependentFixtureInterface
{
    public const SANDBOX_REFERENCE_TAG = 'sandbox-';
    public const NB_SANDBOX = 20;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < self::NB_SANDBOX; $i++) {
            $sandbox = new Sandbox();
            $sandbox->setCourse($this->getReference(CourseFixtures::COURSE_REFERENCE_TAG . rand(0, CourseFixtures::NB_COURSE - 1), Course::class));
            $id = rand((ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR), (ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR + TrainerFixtures::NB_TRAINER) - 1);
            $sandbox->setAuthor($this->getReference(TrainerFixtures::TRAINER_REFERENCE_TAG . $id, Trainer::class));
            $sandbox->setTitle($faker->sentence(6, true));
            $sandbox->setData('[{"id":"text_' . rand(10000, 999999) . '","type":"text","x":' . $faker->randomFloat(15) . ',"y":' . $faker->randomFloat(15) . ',"text":"' . $faker->sentence() . '","fontSize":20,"fill":"' . $faker->hexColor() . '","draggable":true},{"id":"img_' . rand(10000, 999999) . '","type":"image","x":' . $faker->randomFloat(15) . ',"y":' . $faker->randomFloat(15) . ',"src":"data:image\/png;base64,' . base64_encode('blipblop') . '","width":' . $faker->randomFloat(15) . ',"height":' . $faker->randomFloat(15) . ',"rotation":' . $faker->randomFloat(15) . ',"draggable":true,"scaleX":1,"scaleY":1}]');
            $sandbox->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')));
            $sandbox->setUuid($faker->uuid());
            $sandbox->setSlide($faker->randomNumber());

            $manager->persist($sandbox);
            $this->addReference(self::SANDBOX_REFERENCE_TAG . $i, $sandbox);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CourseFixtures::class,
            TraineeFixtures::class,
            TrainerFixtures::class,
            CoordinatorFixtures::class,
            ResponsibleFixtures::class,
        ];
    }
}
