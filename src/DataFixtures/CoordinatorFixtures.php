<?php

namespace App\DataFixtures;

use App\Entity\Avatar;
use App\Entity\Coordinator;
use App\Entity\Responsible;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

// The DependentFixtureInterface is imported to use the getDependencies method to avoid the error of loading fixtures in the wrong order
class CoordinatorFixtures extends Fixture implements DependentFixtureInterface
{
    public const USER_REFERENCE_TAG = 'user-';
    public const COORDINATOR_REFERENCE_TAG = 'coordinator-';
    public const NB_COORDINATOR = 3;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $coordinator = new Coordinator();
        $coordinator->setRole("Coodinatrice");
        $coordinator->setEntranceCode(null);
        $coordinator->setEntranceCodeDate(null);
        $coordinator->setRoles(['ROLE_COORDINATOR']);
        $coordinator->setUsername("sophiepoulakos");
        $coordinator->setPassword(password_hash("adrar", PASSWORD_BCRYPT, ['cost' => 12]));
        $coordinator->setLastName("POULAKOS");
        $coordinator->setFirstName("Sophie");
        $coordinator->setEmail("sophiepoulakos@adrar-formation.com");
        $coordinator->setActivated(true);
        $coordinator->setTmpCode(null);
        $coordinator->setTmpCodeDate(null);
        $coordinator->setAvatar($this->getReference(AvatarFixtures::AVATAR_REFERENCE_TAG . rand(0, AvatarFixtures::NB_AVATAR - 1), Avatar::class));
        $coordinator->setSignature($faker->imageUrl(300, 300, 'signature'));
        $coordinator->setUuid($faker->uuid());
        $coordinator->setPhoneNumber("0" . $faker->unique()->numberBetween(600000000, 799999999));
        $coordinator->setResponsible($this->getReference(ResponsibleFixtures::RESPONSIBLE_REFERENCE_TAG . rand(0, ResponsibleFixtures::NB_RESPONSIBLE - 1), Responsible::class));

        $manager->persist($coordinator);
        for ($i = ResponsibleFixtures::NB_RESPONSIBLE; $i < (ResponsibleFixtures::NB_RESPONSIBLE + self::NB_COORDINATOR); $i++) {
            $coordinator = new Coordinator();
            $coordinator->setRole($faker->words(3, true));
            $coordinator->setEntranceCode($faker->numberBetween(10000, 99999));
            $coordinator->setEntranceCodeDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 day', '+1 week')));
            $coordinator->setRoles(['ROLE_COORDINATOR']);
            $coordinator->setUsername($faker->unique()->userName() . rand(0, 100));
            $sPassword = $faker->password(10);
            $coordinator->setPassword(password_hash($sPassword, PASSWORD_BCRYPT, ['cost' => 12]));
            $coordinator->setLastName($faker->lastName());
            $coordinator->setFirstName($faker->firstName());
            $coordinator->setEmail($faker->unique()->email());
            $coordinator->setActivated($faker->boolean());
            $bIsTemporaryBlocked = (rand(0, 20) == 20);
            $coordinator->setTmpCode($bIsTemporaryBlocked ? rand(100000, 999999) : null);
            $coordinator->setTmpCodeDate($bIsTemporaryBlocked ? DateTimeImmutable::createFromMutable($faker->dateTimeBetween('+1 week', '+2 week')) : null);
            $coordinator->setAvatar($this->getReference(AvatarFixtures::AVATAR_REFERENCE_TAG . rand(0, AvatarFixtures::NB_AVATAR - 1), Avatar::class));
            $coordinator->setSignature($faker->imageUrl(300, 300, 'signature'));
            $coordinator->setUuid($faker->unique()->uuid());
            $coordinator->setPhoneNumber("0" . $faker->unique()->numberBetween(600000000, 799999999));

            $manager->persist($coordinator);

            $this->addReference(self::USER_REFERENCE_TAG . $i, $coordinator);
            $this->addReference(self::COORDINATOR_REFERENCE_TAG . $i, $coordinator);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AvatarFixtures::class,
            ResponsibleFixtures::class,
        ];
    }
}
