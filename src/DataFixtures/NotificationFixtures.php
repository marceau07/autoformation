<?php

namespace App\DataFixtures;

use App\Entity\Coordinator;
use App\Entity\Course;
use App\Entity\Message;
use App\Entity\Notification;
use App\Entity\Responsible;
use App\Entity\Trainee;
use App\Entity\Trainer;
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
            $isACourse = $faker->boolean(35);
            $isAHomework = $faker->boolean(27);
            $isANewFeature = $faker->boolean(5);
            $isAnInternshipFile = $faker->boolean(15);

            $notification = new Notification();
            $course = $this->getReference(CourseFixtures::COURSE_REFERENCE_TAG . rand(0, CourseFixtures::NB_COURSE - 1), Course::class);
            $courseResource = $this->getReference(CourseFixtures::COURSE_REFERENCE_TAG . rand(0, CourseFixtures::NB_COURSE - 1), Course::class);
            $message = $this->getReference(MessageFixtures::MESSAGE_REFERENCE_TAG . rand(0, MessageFixtures::NB_MESSAGE - 1), Message::class);

            $notification->setOrigin($faker->text(150));
            $notification->setMessage($isACourse ? $course->getModule()->getLabel() : ($isAHomework ? $courseResource->getTitle() : $message->getContent()));
            $notification->setLink(
                $isACourse ? '/embed/' . $course->getLink() : ($isAHomework ? '/embed/' . $courseResource->getLink() . '/#homework' :
                    '/message/' . strtolower(basename(str_replace('\\', '/', ($message->getPeople()) !== null && !empty($message->getPeople()) ? get_class($message->getPeople()) : get_class($message->getCohort())))) . "/" . (($message->getPeople()) !== null && !empty($message->getPeople()) ? $message->getPeople()->getUuid() : $message->getCohort()->getUuid()))
            );
            $notification->setDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')));
            $notification->setCategory($isACourse ? 'new_course' : ($isAHomework ? 'homework_to_do' : ($isANewFeature ? 'new_features' : ($isAnInternshipFile ? 'new_internship' : 'new_message'))));
            $id = rand(0, (ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR + TrainerFixtures::NB_TRAINER + TraineeFixtures::NB_TRAINEE) - 1);
            $notification->setUser(
                $this->getReference(TrainerFixtures::USER_REFERENCE_TAG . $id, ($id < ResponsibleFixtures::NB_RESPONSIBLE ? Responsible::class : 
                    ($id < (ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR) ? Coordinator::class : 
                    ($id < (ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR + TrainerFixtures::NB_TRAINER) ? Trainer::class : Trainee::class)))
                )
            );

            $manager->persist($notification);
            $this->addReference(self::NOTIFICATION_REFERENCE_TAG . $i, $notification);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            MessageFixtures::class,
            CourseFixtures::class,
            CourseResourceFixtures::class,
            TraineeFixtures::class,
            TrainerFixtures::class,
            CoordinatorFixtures::class,
            ResponsibleFixtures::class,
        ];
    }
}
