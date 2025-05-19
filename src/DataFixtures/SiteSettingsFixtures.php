<?php

namespace App\DataFixtures;

use App\Entity\SiteSettings;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SiteSettingsFixtures extends Fixture
{
    public const SITE_SETTINGS_REFERENCE_TAG = 'site-settings-';
    public const NB_SITE_SETTINGS = 1;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $siteSettings = new SiteSettings();
        $siteSettings->setMaintenanceMode($faker->boolean());
        $siteSettings->setLogoPath($faker->imageUrl(70, 70, 'logo'));
        $siteSettings->setLogoName($faker->name());
        $siteSettings->setPlatformName($faker->name());
        $siteSettings->setPrimaryColor($faker->hexColor());
        $siteSettings->setSecondaryColor($faker->hexColor());
        $siteSettings->setTertiaryColor($faker->hexColor());
        $siteSettings->setQuaternaryColor($faker->hexColor());
        $siteSettings->setLightenColor($faker->hexColor());
        $siteSettings->setDarkenColor($faker->hexColor());

        $manager->persist($siteSettings);
        $this->addReference(self::SITE_SETTINGS_REFERENCE_TAG . '0', $siteSettings);

        $manager->flush();
    }
}
