<?php

namespace App\DataFixtures;

use App\Entity\Internship;
use App\Entity\Message;
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
            $isSendByATrainer = $faker->boolean(35);
            $isMessageForSession = $faker->boolean(27);
            $isMessageForTrainer = $faker->boolean(60);

            $message->setSendTrainee(!$isSendByATrainer ? $this->getReference(TraineeFixtures::TRAINEE_REFERENCE_TAG . rand(TrainerFixtures::NB_TRAINER, (TrainerFixtures::NB_TRAINER + TraineeFixtures::NB_TRAINEE - 1))) : null);
            $message->setSendTrainer($isSendByATrainer ? $this->getReference(TrainerFixtures::TRAINER_REFERENCE_TAG . rand(0, TrainerFixtures::NB_TRAINER - 1)) : null);
            $message->setTrainee(!$isMessageForSession && !$isMessageForTrainer ? $this->getReference(TraineeFixtures::TRAINEE_REFERENCE_TAG . rand(TrainerFixtures::NB_TRAINER, (TrainerFixtures::NB_TRAINER + TraineeFixtures::NB_TRAINEE - 1))) : null);
            $message->setTrainer(!$isMessageForSession && $isMessageForTrainer ? $this->getReference(TrainerFixtures::TRAINER_REFERENCE_TAG . rand(0, TrainerFixtures::NB_TRAINER - 1)) : null);
            $message->setCohort($isMessageForSession && !$isMessageForTrainer ? $this->getReference(CohortFixtures::COHORT_REFERENCE_TAG . rand(0, CohortFixtures::NB_COHORT - 1)) : null);
            $message->setContent($faker->text(250));
            $message->setDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')));
            $message->setReaded($faker->boolean(90));
            
            $manager->persist($message);
            $this->addReference(self::MESSAGE_REFERENCE_TAG . $i, $message);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CohortFixtures::class, 
            TraineeFixtures::class, 
            TrainerFixtures::class
        ];
    }
}
