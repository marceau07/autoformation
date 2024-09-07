<?php

namespace App\DataFixtures;

use App\Entity\Notification;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

// The DependentFixtureInterface is imported to use the getDependencies method to avoid the error of loading fixtures in the wrong order
class NotificationFixtures extends Fixture implements DependentFixtureInterface
{
    public const NOTIFICATION_REFERENCE_TAG = 'notification-';
    public const NB_NOTIFICATION = 1000;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::NB_NOTIFICATION; $i++) {
            $notification = new Notification();
            $isACourse = $faker->boolean(35);
            $isAHomework = $faker->boolean(27);
            $isATrainer = $faker->boolean(40);
            $course = $this->getReference(CourseFixtures::COURSE_REFERENCE_TAG . rand(0, CourseFixtures::NB_COURSE - 1));
            $courseResource = $this->getReference(CourseFixtures::COURSE_REFERENCE_TAG . rand(0, CourseFixtures::NB_COURSE - 1));
            $message = $this->getReference(MessageFixtures::MESSAGE_REFERENCE_TAG . rand(0, MessageFixtures::NB_MESSAGE - 1));

            $notification->setOrigin($faker->text(150));
            $notification->setMessage($isACourse ? $course->getModule()->getLabel() : ($isAHomework ? $courseResource->getTitle() : $message->getContent()));
            $notification->setLink(
                $isACourse ? '/embed/' . $course->getLink() : ($isAHomework ? '/embed/' . $courseResource->getLink() . '/#homework' :
                        // '/message/' . ($isATrainer ? $message->getSendTrainer() : $message->getSendTrainee()->getUuid()))
                        '/message/' . $faker->unique()->uuid())
            );
            $notification->setDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')));
            $notification->setCategory($isACourse ? 'new_course' : ($isAHomework ? 'homework_to_do' : 'new_message'));
            $notification->setUser($this->getReference(TraineeFixtures::USER_REFERENCE_TAG . rand(0, (TrainerFixtures::NB_TRAINER + TraineeFixtures::NB_TRAINEE - 1))));

            $manager->persist($notification);
            $this->addReference(self::NOTIFICATION_REFERENCE_TAG . $i, $notification);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            MessageFixtures::class,
            CourseFixtures::class,
            // CourseResourceFixtures::class, // TODO: add the fixture after creating the CourseResourceFixtures entity
            TraineeFixtures::class,
            TrainerFixtures::class
        ];
    }
}
