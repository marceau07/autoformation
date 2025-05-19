<?php

namespace App\DataFixtures;

use App\Entity\Coordinator;
use App\Entity\Feedback;
use App\Entity\FeedbackCategory;
use App\Entity\Responsible;
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
            $id = rand(0, (ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR + TrainerFixtures::NB_TRAINER + TraineeFixtures::NB_TRAINEE) - 1);
            $feedback->setUser(
                $this->getReference(TrainerFixtures::USER_REFERENCE_TAG . $id, ($id < ResponsibleFixtures::NB_RESPONSIBLE ? Responsible::class : 
                    ($id < (ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR) ? Coordinator::class : 
                    ($id < (ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR + TrainerFixtures::NB_TRAINER) ? Trainer::class : Trainee::class)))
                )
            );
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
            ResponsibleFixtures::class,
            CoordinatorFixtures::class,
            TrainerFixtures::class,
            TraineeFixtures::class
        ];
    }
}
