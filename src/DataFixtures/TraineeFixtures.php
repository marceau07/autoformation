<?php

namespace App\DataFixtures;

use App\Entity\Trainee;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

// The DependentFixtureInterface is imported to use the getDependencies method to avoid the error of loading fixtures in the wrong order
class TraineeFixtures extends Fixture implements DependentFixtureInterface
{
    public const USER_REFERENCE_TAG = 'user-';
    public const TRAINEE_REFERENCE_TAG = 'trainee-';
    public const NB_TRAINEE = 20;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        for ($i = TrainerFixtures::NB_TRAINER; $i < (TrainerFixtures::NB_TRAINER + self::NB_TRAINEE); $i++) {
            $trainee = new Trainee();
            $trainee->setRoles(['ROLE_TRAINEE']);
            $sPassword = $faker->password(10);
            $trainee->setPasswordSave(password_hash($sPassword, PASSWORD_BCRYPT, ['cost' => 12]));
            $trainee->setDocuments('{}');
            $trainee->setDiploma(rand(-7, 1));
            $trainee->setCohort($this->getReference(CohortFixtures::COHORT_REFERENCE_TAG . rand(0, CohortFixtures::NB_COHORT - 1)));
            $trainee->setUsername($faker->unique()->userName());
            $trainee->setPassword(password_hash($sPassword, PASSWORD_BCRYPT, ['cost' => 12]));
            $trainee->setLastName($faker->lastName());
            $trainee->setFirstName($faker->firstName());
            $trainee->setEmail($faker->unique()->email());
            $trainee->setActivated($faker->boolean());
            $bIsTemporaryBlocked = (rand(0, 20) == 20);
            $trainee->setTmpCode($bIsTemporaryBlocked ? rand(100000, 999999) : null);
            $trainee->setTmpCodeDate($bIsTemporaryBlocked ? DateTimeImmutable::createFromMutable($faker->dateTimeBetween('+1 week', '+2 week')) : null);
            $trainee->setAvatar($this->getReference(AvatarFixtures::AVATAR_REFERENCE_TAG . rand(0, AvatarFixtures::NB_AVATAR - 1)));
            // $trainee->setSignature($sPassword);
            $trainee->setSignature($faker->imageUrl(300, 300, 'signature'));
            $trainee->setUuid($faker->unique()->uuid());
            $trainee->setPhoneNumber("0" . $faker->unique()->numberBetween(600000000, 799999999));

            $manager->persist($trainee);
            $this->addReference(self::USER_REFERENCE_TAG . $i, $trainee);
            $this->addReference(self::TRAINEE_REFERENCE_TAG . $i, $trainee);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            AvatarFixtures::class,
            CohortFixtures::class
        ];
    }
}
