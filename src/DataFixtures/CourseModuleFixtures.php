<?php

namespace App\DataFixtures;

use App\Entity\CourseModule;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CourseModuleFixtures extends Fixture
{
    public const COURSE_MODULE_REFERENCE_TAG = 'course-module-';
    public const NB_COURSE_MODULE = 100;
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::NB_COURSE_MODULE; $i++) {
            $cm = new CourseModule();
            $cm->setLabel($faker->countryCode() . ' - ' . $faker->word());
            $cm->setPosition(rand(0, self::NB_COURSE_MODULE));
            $cm->setUuid($faker->uuid());
            $cm->setIllustration($this->params->get(name: 'PLACEHOLDER_LINK') . "300x300/" . str_replace('#', '', $faker->safeHexColor()) . "/" . str_replace('#', '', $faker->safeHexColor()) . ".png" . "?text=module");

            $manager->persist($cm);
            $this->addReference(self::COURSE_MODULE_REFERENCE_TAG . $i, $cm);
        }

        $manager->flush();
    }
}
