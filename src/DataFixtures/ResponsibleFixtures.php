<?php

namespace App\DataFixtures;

use App\Entity\Avatar;
use App\Entity\Responsible;
use App\Entity\Sector;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

// The DependentFixtureInterface is imported to use the getDependencies method to avoid the error of loading fixtures in the wrong order
class ResponsibleFixtures extends Fixture implements DependentFixtureInterface
{
    public const USER_REFERENCE_TAG = 'user-';
    public const RESPONSIBLE_REFERENCE_TAG = 'responsible-';
    public const NB_RESPONSIBLE = 20;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $responsible = new Responsible();
        $responsible->setRole("Responsable");
        $responsible->setEntranceCode(null);
        $responsible->setEntranceCodeDate(null);
        $responsible->setSector($this->getReference(SectorFixtures::SECTOR_REFERENCE_TAG . rand(0, SectorFixtures::NB_SECTOR - 1), Sector::class));
        $responsible->setRoles(['ROLE_RESPONSIBLE']);
        $responsible->setUsername("jeromechretienne");
        $responsible->setPassword(password_hash("adrar", PASSWORD_BCRYPT, ['cost' => 12]));
        $responsible->setLastName("CHRETIENNE");
        $responsible->setFirstName("Jérôme");
        $responsible->setEmail("jeromechretienne@adrar-formation.com");
        $responsible->setActivated(true);
        $responsible->setTmpCode(null);
        $responsible->setTmpCodeDate(null);
        $responsible->setAvatar($this->getReference(AvatarFixtures::AVATAR_REFERENCE_TAG . rand(0, AvatarFixtures::NB_AVATAR - 1), Avatar::class));
        $responsible->setSignature($faker->imageUrl(300, 300, 'signature'));
        $responsible->setUuid($faker->uuid());
        $responsible->setPhoneNumber("0" . $faker->unique()->numberBetween(600000000, 799999999));

        $manager->persist($responsible);
        for ($i = 0; $i < self::NB_RESPONSIBLE; $i++) {
            $responsible = new Responsible();
            $responsible->setRole($faker->words(3, true));
            $responsible->setEntranceCode($faker->numberBetween(10000, 99999));
            $responsible->setEntranceCodeDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 day', '+1 week')));
            $responsible->setSector($this->getReference(SectorFixtures::SECTOR_REFERENCE_TAG . rand(0, SectorFixtures::NB_SECTOR - 1), Sector::class));
            $responsible->setRoles(['ROLE_RESPONSIBLE']);
            $responsible->setUsername($faker->unique()->userName() . rand(0, 100));
            $sPassword = $faker->password(10);
            $responsible->setPassword(password_hash($sPassword, PASSWORD_BCRYPT, ['cost' => 12]));
            $responsible->setLastName($faker->lastName());
            $responsible->setFirstName($faker->firstName());
            $responsible->setEmail($faker->unique()->email());
            $responsible->setActivated($faker->boolean());
            $bIsTemporaryBlocked = (rand(0, 20) == 20);
            $responsible->setTmpCode($bIsTemporaryBlocked ? rand(100000, 999999) : null);
            $responsible->setTmpCodeDate($bIsTemporaryBlocked ? DateTimeImmutable::createFromMutable($faker->dateTimeBetween('+1 week', '+2 week')) : null);
            $responsible->setAvatar($this->getReference(AvatarFixtures::AVATAR_REFERENCE_TAG . rand(0, AvatarFixtures::NB_AVATAR - 1), Avatar::class));
            // $responsible->setSignature($sPassword);
            $responsible->setSignature($faker->imageUrl(300, 300, 'signature'));
            $responsible->setUuid($faker->unique()->uuid());
            $responsible->setPhoneNumber("0" . $faker->unique()->numberBetween(600000000, 799999999));

            $manager->persist($responsible);

            $this->addReference(self::USER_REFERENCE_TAG . $i, $responsible);
            $this->addReference(self::RESPONSIBLE_REFERENCE_TAG . $i, $responsible);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AvatarFixtures::class,
            SectorFixtures::class
        ];
    }
}
