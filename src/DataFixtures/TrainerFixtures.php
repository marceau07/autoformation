<?php

namespace App\DataFixtures;

use App\Entity\Trainer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

// The DependentFixtureInterface is imported to use the getDependencies method to avoid the error of loading fixtures in the wrong order
class TrainerFixtures extends Fixture implements DependentFixtureInterface
{
    public const USER_REFERENCE_TAG = 'user-';
    public const TRAINER_REFERENCE_TAG = 'trainer-';
    public const NB_TRAINER = 20;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $trainer = new Trainer();
        $trainer->setRole("Formateur référent");
        $trainer->setEntranceCode(null);
        $trainer->setEntranceCodeDate(null);
        $trainer->setSector($this->getReference(SectorFixtures::SECTOR_REFERENCE_TAG . rand(0, SectorFixtures::NB_SECTOR - 1)));
        $trainer->setRoles(['ROLE_TRAINER']);
        $trainer->setUsername("marceaurodrigues");
        $trainer->setPassword(password_hash("adrar", PASSWORD_BCRYPT, ['cost' => 12]));
        $trainer->setLastName("RODRIGUES");
        $trainer->setFirstName("Marceau");
        $trainer->setEmail("marceaurodrigues@adrar-formation.com");
        $trainer->setActivated(true);
        $trainer->setTmpCode(null);
        $trainer->setTmpCodeDate(null);
        $trainer->setAvatar($this->getReference(AvatarFixtures::AVATAR_REFERENCE_TAG . rand(0, AvatarFixtures::NB_AVATAR - 1)));
        $trainer->setSignature($faker->imageUrl(300, 300, 'signature'));
        $trainer->setUuid($faker->uuid());
        $trainer->setPhoneNumber("0" . $faker->unique()->numberBetween(600000000, 799999999));

        $manager->persist($trainer);
        for ($i = 0; $i < self::NB_TRAINER; $i++) {
            $trainer = new Trainer();
            $trainer->setRole($faker->words(3, true));
            $trainer->setEntranceCode($faker->numberBetween(10000, 99999));
            $trainer->setEntranceCodeDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 day', '+1 week')));
            $trainer->setSector($this->getReference(SectorFixtures::SECTOR_REFERENCE_TAG . rand(0, SectorFixtures::NB_SECTOR - 1)));
            $trainer->setRoles(['ROLE_TRAINER']);
            $trainer->setUsername($faker->unique()->userName());
            $sPassword = $faker->password(10);
            $trainer->setPassword(password_hash($sPassword, PASSWORD_BCRYPT, ['cost' => 12]));
            $trainer->setLastName($faker->lastName());
            $trainer->setFirstName($faker->firstName());
            $trainer->setEmail($faker->unique()->email());
            $trainer->setActivated($faker->boolean());
            $bIsTemporaryBlocked = (rand(0, 20) == 20);
            $trainer->setTmpCode($bIsTemporaryBlocked ? rand(100000, 999999) : null);
            $trainer->setTmpCodeDate($bIsTemporaryBlocked ? DateTimeImmutable::createFromMutable($faker->dateTimeBetween('+1 week', '+2 week')) : null);
            $trainer->setAvatar($this->getReference(AvatarFixtures::AVATAR_REFERENCE_TAG . rand(0, AvatarFixtures::NB_AVATAR - 1)));
            // $trainer->setSignature($sPassword);
            $trainer->setSignature($faker->imageUrl(300, 300, 'signature'));
            $trainer->setUuid($faker->unique()->uuid());
            $trainer->setPhoneNumber("0" . $faker->unique()->numberBetween(600000000, 799999999));

            $manager->persist($trainer);
            $this->addReference(self::USER_REFERENCE_TAG . $i, $trainer);
            $this->addReference(self::TRAINER_REFERENCE_TAG . $i, $trainer);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            AvatarFixtures::class,
            SectorFixtures::class
        ];
    }
}
