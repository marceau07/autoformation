<?php

namespace App\DataFixtures;

use App\Entity\SiteSettings;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SiteSettingsFixtures extends Fixture
{
    public const SITE_SETTINGS_REFERENCE_TAG = 'site-settings-';
    public const NB_SITE_SETTINGS = 1;
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $siteSettings = new SiteSettings();
        $siteSettings->setMaintenanceMode($faker->boolean());
        $siteSettings->setLogoPath($this->params->get(name: 'PLACEHOLDER_LINK') . "70x70/" . str_replace('#', '', $faker->safeHexColor()) . "/" . str_replace('#', '', $faker->safeHexColor()) . ".jpg" . "?text=logo");
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
