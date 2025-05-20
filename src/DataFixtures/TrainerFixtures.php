<?php

namespace App\DataFixtures;

use App\Entity\Avatar;
use App\Entity\Coordinator;
use App\Entity\Trainer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

// The DependentFixtureInterface is imported to use the getDependencies method to avoid the error of loading fixtures in the wrong order
class TrainerFixtures extends Fixture implements DependentFixtureInterface
{
    public const USER_REFERENCE_TAG = 'user-';
    public const TRAINER_REFERENCE_TAG = 'trainer-';
    public const NB_TRAINER = 20;
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $trainer = new Trainer();
        $trainer->setRole("Formateur référent");
        $trainer->setEntranceCode(null);
        $trainer->setEntranceCodeDate(null);
        $trainer->setRoles(['ROLE_TRAINER']);
        $trainer->setUsername("leformateur");
        $trainer->setPassword(password_hash("T3sts!", PASSWORD_BCRYPT, ['cost' => 12]));
        $trainer->setLastName("FORMATEUR");
        $trainer->setFirstName("Le");
        $trainer->setEmail("leformateur@yopmail.com");
        $trainer->setActivated(true);
        $trainer->setTmpCode(null);
        $trainer->setTmpCodeDate(null);
        $trainer->setAvatar($this->getReference(AvatarFixtures::AVATAR_REFERENCE_TAG . rand(0, AvatarFixtures::NB_AVATAR - 1), Avatar::class));
        $trainer->setSignature($this->params->get(name: 'PLACEHOLDER_LINK') . "300x300/" . str_replace('#', '', $faker->safeHexColor()) . "/" . str_replace('#', '', $faker->safeHexColor()) . ".png" . "?text=signature");
        $trainer->setUuid($faker->uuid());
        $trainer->setPhoneNumber("0" . $faker->unique()->numberBetween(600000000, 799999999));

        $manager->persist($trainer);

        $this->addReference(self::USER_REFERENCE_TAG . (ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR + self::NB_TRAINER) - 1, $trainer);
        $this->addReference(self::TRAINER_REFERENCE_TAG . (ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR + self::NB_TRAINER) - 1, $trainer);
        for ($i = (ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR); $i < (ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR + self::NB_TRAINER) - 1; $i++) {
            $trainer = new Trainer();
            $trainer->setRole($faker->words(3, true));
            $trainer->setEntranceCode($faker->numberBetween(10000, 99999));
            $trainer->setEntranceCodeDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 day', '+1 week')));
            $trainer->setRoles(['ROLE_TRAINER']);
            $trainer->setUsername($faker->unique()->userName() . rand(0, 100));
            $sPassword = $faker->password(10);
            $trainer->setPassword(password_hash($sPassword, PASSWORD_BCRYPT, ['cost' => 12]));
            $trainer->setLastName($faker->lastName());
            $trainer->setFirstName($faker->firstName());
            $trainer->setEmail($faker->unique()->email());
            $trainer->setActivated($faker->boolean());
            $bIsTemporaryBlocked = (rand(0, 20) == 20);
            $trainer->setTmpCode($bIsTemporaryBlocked ? rand(100000, 999999) : null);
            $trainer->setTmpCodeDate($bIsTemporaryBlocked ? DateTimeImmutable::createFromMutable($faker->dateTimeBetween('+1 week', '+2 week')) : null);
            $trainer->setAvatar($this->getReference(AvatarFixtures::AVATAR_REFERENCE_TAG . rand(0, AvatarFixtures::NB_AVATAR - 1), Avatar::class));
            $trainer->setSignature($this->params->get(name: 'PLACEHOLDER_LINK') . "300x300/" . str_replace('#', '', $faker->safeHexColor()) . "/" . str_replace('#', '', $faker->safeHexColor()) . ".png" . "?text=signature");
            $trainer->setUuid($faker->unique()->uuid());
            $trainer->setPhoneNumber("0" . $faker->unique()->numberBetween(600000000, 799999999));
            $trainer->setCoordinator($this->getReference(CoordinatorFixtures::COORDINATOR_REFERENCE_TAG . rand((ResponsibleFixtures::NB_RESPONSIBLE), (ResponsibleFixtures::NB_RESPONSIBLE + CoordinatorFixtures::NB_COORDINATOR) - 1), Coordinator::class));

            $manager->persist($trainer);

            $this->addReference(self::USER_REFERENCE_TAG . $i, $trainer);
            $this->addReference(self::TRAINER_REFERENCE_TAG . $i, $trainer);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AvatarFixtures::class,
            CoordinatorFixtures::class,
        ];
    }
}
