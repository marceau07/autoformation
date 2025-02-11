<?php

namespace App\DataFixtures;

use App\Entity\Feedback;
use App\Entity\FeedbackCategory;
use App\Entity\Trainee;
use App\Entity\Trainer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class FeedbackFixtures extends Fixture implements DependentFixtureInterface
{
    public const FEEDBACK_REFERENCE_TAG = 'feedback-';
    public const NB_FEEDBACK = 100;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::NB_FEEDBACK; $i++) {
            $feedback = new Feedback();
            $feedback->setCategory($this->getReference(FeedbackCategoryFixtures::FEEDBACK_CATEGORY_REFERENCE_TAG . rand(0, FeedbackCategoryFixtures::NB_FEEDBACK_CATEGORY - 1), FeedbackCategory::class));
            $feedback->setAnnotation($faker->paragraphs(3, true));
            $luck = $faker->boolean(20);
            $feedback->setUser(
                $this->getReference(($luck ? 
                    TrainerFixtures::TRAINER_REFERENCE_TAG . rand(0, TrainerFixtures::NB_TRAINER - 1) : 
                    TraineeFixtures::TRAINEE_REFERENCE_TAG . rand(TrainerFixtures::NB_TRAINER, ((TrainerFixtures::NB_TRAINER + TraineeFixtures::NB_TRAINEE) - 1))), 
                    // Parameter number 2 of getReference()
                    ($luck ? Trainer::class : Trainee::class)));
            $feedback->setLink($faker->url());
            $feedback->setWeight($faker->numberBetween(1, 10));

            $manager->persist($feedback);
            $this->addReference(self::FEEDBACK_REFERENCE_TAG . $i, $feedback);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            FeedbackCategoryFixtures::class, 
            TrainerFixtures::class,
            TraineeFixtures::class
        ];
    }
}
