<?php

namespace App\DataFixtures;

use App\Entity\Sector;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SectorFixtures extends Fixture
{
    public const SECTOR_REFERENCE_TAG = 'sector-';
    public const NB_SECTOR = 5;
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::NB_SECTOR; $i++) {
            $sector = new Sector();
            $sector->setLabel($faker->name());
            $sector->setLogo($this->params->get(name: 'PLACEHOLDER_LINK') . "70x70/" . str_replace('#', '', $faker->safeHexColor()) . "/" . str_replace('#', '', $faker->safeHexColor()) . ".png" . "?text=logo");

            $manager->persist($sector);
            $this->addReference(self::SECTOR_REFERENCE_TAG . $i, $sector);
        }

        $manager->flush();
    }
}
