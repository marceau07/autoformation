<?php

namespace App\DataFixtures;

use App\Entity\Internship;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

// The DependentFixtureInterface is imported to use the getDependencies method to avoid the error of loading fixtures in the wrong order
class InternshipFixtures extends Fixture implements DependentFixtureInterface
{
    public const INTERNSHIP_REFERENCE_TAG = 'internship-';
    public const NB_INTERNSHIP = 100;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        $usedCombinations = [];
        for ($i = 0; $i < self::NB_INTERNSHIP; $i++) {
            do {
                $traineeIndex = rand(TrainerFixtures::NB_TRAINER, (TrainerFixtures::NB_TRAINER + TraineeFixtures::NB_TRAINEE - 1));
                $prospectIndex = rand(0, ProspectFixtures::NB_PROSPECT - 1);
                $combination = $traineeIndex . '|' . $prospectIndex;
            } while (in_array($combination, $usedCombinations));
            $usedCombinations[] = $combination;

            $internship = new Internship();
            $internship->setTrainee($this->getReference(TraineeFixtures::TRAINEE_REFERENCE_TAG . $traineeIndex));
            $internship->setProspect($this->getReference(ProspectFixtures::PROSPECT_REFERENCE_TAG . $prospectIndex));
            $internship->setTutorLastName($faker->lastName());
            $internship->setTutorFirstName($faker->firstName());
            $internship->setTutorEmail($faker->unique()->email());  
            $internship->setTutorPhoneNumber("0" . $faker->unique()->numberBetween(600000000, 799999999));

            $manager->persist($internship);
            $this->addReference(self::INTERNSHIP_REFERENCE_TAG . $i, $internship);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProspectFixtures::class, 
            TraineeFixtures::class
        ];
    }
}
