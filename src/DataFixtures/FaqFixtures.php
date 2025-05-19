<?php

namespace App\DataFixtures;

use App\Entity\Faq;
use App\Entity\Sector;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class FaqFixtures extends Fixture implements DependentFixtureInterface
{
    public const FAQ_REFERENCE_TAG = 'faq-';
    public const NB_FAQ = 100;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::NB_FAQ; $i++) {
            $faq = new Faq();
            $faq->setSector(($faker->boolean(80)) ? $this->getReference(SectorFixtures::SECTOR_REFERENCE_TAG . rand(0, SectorFixtures::NB_SECTOR - 1), Sector::class) : null);
            $faq->setTheme($faker->randomElement(['global', 'salle', 'cuisine', 'bureau', 'extérieur', 'parking', 'autre']));
            $faq->setTitle($faker->title());
            $faq->setContent($faker->paragraphs(3, true));
            $faq->setVisibility($faker->boolean(70));
            $faq->setPriority($faker->numberBetween(0, 10));

            $manager->persist($faq);
            $this->addReference(self::FAQ_REFERENCE_TAG . $i, $faq);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SectorFixtures::class
        ];
    }
}
