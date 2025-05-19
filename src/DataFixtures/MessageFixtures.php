<?php

namespace App\DataFixtures;

use App\Entity\Cohort;
use App\Entity\Coordinator;
use App\Entity\Message;
use App\Entity\Responsible;
use App\Entity\Trainee;
use App\Entity\Trainer;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

// The DependentFixtureInterface is imported to use the getDependencies method to avoid the error of loading fixtures in the wrong order
class MessageFixtures extends Fixture implements DependentFixtureInterface
{
  public const MESSAGE_REFERENCE_TAG = 'message-';
  public const NB_MESSAGE = 1000;

  public function load(ObjectManager $manager): void
  {
    $faker = Factory::create('fr_FR');

    for ($i = 0; $i < self::NB_MESSAGE; $i++) {
      $message = new Message();
      $isSentMessage = $faker->boolean(35);
      $isMessageForSession = $faker->boolean(70);
      $isMessageWithFile = $faker->boolean(30);
      $id = rand(0, (ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR + TrainerFixtures::NB_TRAINER + TraineeFixtures::NB_TRAINEE) - 1);
      $message->setSendPeople(!$isSentMessage ?
          $this->getReference(TrainerFixtures::USER_REFERENCE_TAG . $id, ($id < ResponsibleFixtures::NB_RESPONSIBLE ? Responsible::class : 
              ($id < (ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR) ? Coordinator::class : 
              ($id < (ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR + TrainerFixtures::NB_TRAINER) ? Trainer::class : Trainee::class)))
          ) : null
      );
      if ($isMessageForSession) {
        $message->setCohort(
          $this->getReference(CohortFixtures::COHORT_REFERENCE_TAG . rand(0, CohortFixtures::NB_COHORT - 1), Cohort::class)
        );
        $message->setPeople(null);
      } else {
        $message->setPeople(
          $this->getReference(TrainerFixtures::USER_REFERENCE_TAG . $id, ($id < ResponsibleFixtures::NB_RESPONSIBLE ? Responsible::class : 
              ($id < (ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR) ? Coordinator::class : 
              ($id < (ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR + TrainerFixtures::NB_TRAINER) ? Trainer::class : Trainee::class)))
          )
        );
        $message->setCohort(null);
      }
      $message->setContent($faker->text(250));
      $message->setDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')));
      $message->setReaded($faker->boolean(90));
      $mimeType = $faker->randomElement(['png', 'jpeg', 'jpg']); // TODO: add more mime types for files and videos
      $message->setDocument($isMessageWithFile ? $faker->imageUrl(300, 300, 'file', true, null, false, $mimeType) : null);
      $message->setMimeType($isMessageWithFile ? $mimeType : null);

      $manager->persist($message);
      $this->addReference(self::MESSAGE_REFERENCE_TAG . $i, $message);
    }
    $manager->flush();
  }

  public function getDependencies(): array
  {
    return [
      CohortFixtures::class,
      TraineeFixtures::class,
      TrainerFixtures::class,
      CoordinatorFixtures::class,
      ResponsibleFixtures::class,
    ];
  }
}
