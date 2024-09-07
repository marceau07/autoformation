<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Avatar;

class AvatarFixtures extends Fixture
{
    public const AVATAR_REFERENCE_TAG = 'avatar-';
    public const NB_AVATAR = 10;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::NB_AVATAR; $i++) {
            $avatar = new Avatar();
            $avatar->setLabel($faker->name());
            $avatar->setLink($faker->imageUrl(300, 300, 'people'));

            $manager->persist($avatar);
            $this->addReference(self::AVATAR_REFERENCE_TAG . $i, $avatar);
        }

        $manager->flush();
    }
}
