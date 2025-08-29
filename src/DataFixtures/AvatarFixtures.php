<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Avatar;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AvatarFixtures extends Fixture
{
    public const AVATAR_REFERENCE_TAG = 'avatar-';
    public const NB_AVATAR = 10;
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::NB_AVATAR; $i++) {
            $avatar = new Avatar();
            $avatar->setLabel($faker->name());
            $avatar->setLink($this->params->get(name: 'PLACEHOLDER_LINK') . "300x300/" . str_replace('#', '', $faker->safeHexColor()) . "/" . str_replace('#', '', $faker->safeHexColor()) . ".png" . "?text=avatar");

            $manager->persist($avatar);
            $this->addReference(self::AVATAR_REFERENCE_TAG . $i, $avatar);
        }

        $manager->flush();
    }
}
