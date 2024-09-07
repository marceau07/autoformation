<?php

namespace App\DataFixtures;

use App\Entity\Prospect;
use App\Entity\Trainer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use DateTimeImmutable;

class ProspectFixtures extends Fixture
{
    public const PROSPECT_REFERENCE_TAG = 'prospect-';
    public const NB_PROSPECT = 100;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::NB_PROSPECT; $i++) {
            $prospect = new Prospect();
            $prospect->setName($faker->company());
            $prospect->setSiren($faker->unique()->numberBetween(100000000, 999999999));
            $prospect->setNic($faker->unique()->numberBetween(10000, 99999));
            $prospect->setNumber($faker->numberBetween(1000, 9999));
            $prospect->setStreet($faker->streetAddress());
            $prospect->setPostalCode($faker->postcode());
            $prospect->setCity($faker->city());
            $prospect->setCountry($faker->countryCode());
            $prospect->setEmail($faker->unique()->email());
            $prospect->setAdditionalAddress($faker->secondaryAddress());
            $prospect->setPhoneNumber("0" . $faker->unique()->numberBetween(600000000, 799999999));
            $prospect->setPhoneNumberBis("0" . $faker->unique()->numberBetween(400000000, 499999999));

            $manager->persist($prospect);
            $this->addReference(self::PROSPECT_REFERENCE_TAG . $i, $prospect);
        }
        $manager->flush();
    }
}
