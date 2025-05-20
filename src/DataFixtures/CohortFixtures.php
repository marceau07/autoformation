<?php

namespace App\DataFixtures;

use App\Entity\Cohort;
use App\Entity\Trainer;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CohortFixtures extends Fixture implements DependentFixtureInterface
{
    public const COHORT_REFERENCE_TAG = 'cohort-';
    public const NB_COHORT = 5;
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::NB_COHORT; $i++) {
            $cohort = new Cohort();
            $cohort->setTrainer($this->getReference(TrainerFixtures::TRAINER_REFERENCE_TAG . rand((ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR), (ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR + TrainerFixtures::NB_TRAINER) - 1), Trainer::class));
            $cohort->setName($faker->word());
            $cohort->setAcronym($faker->word());
            $cohort->setShield($this->params->get(name: 'PLACEHOLDER_LINK') . "640x480/" . str_replace('#', '', $faker->safeHexColor()) . "/" . str_replace('#', '', $faker->safeHexColor()) . ".png" . "?text=shield");
            $cohort->setDocuments('{}');
            $cohort->setStartDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 month', '+1 month')));
            $cohort->setFinishDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('+8 month', '+10 month')));
            $cohort->setUuid($faker->uuid());

            $manager->persist($cohort);
            $this->addReference(self::COHORT_REFERENCE_TAG . $i, $cohort);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TrainerFixtures::class
        ];
    }
}
