<?php

namespace App\DataFixtures;

use App\Entity\Sector;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SectorFixtures extends Fixture
{
    public const SECTOR_REFERENCE_TAG = 'sector-';
    public const NB_SECTOR = 5;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::NB_SECTOR; $i++) {
            $sector = new Sector();
            $sector->setLabel($faker->name());
            $sector->setLogo($faker->imageUrl(70, 70, 'logo'));

            $manager->persist($sector);
            $this->addReference(self::SECTOR_REFERENCE_TAG . $i, $sector);
        }

        $manager->flush();
    }
}
