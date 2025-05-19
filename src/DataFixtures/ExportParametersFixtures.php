<?php

namespace App\DataFixtures;

use App\Entity\ExportParameter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ExportParametersFixtures extends Fixture
{
    public const EXPORT_PARAMETER_REFERENCE_TAG = 'export-parameter-';
    public const NB_EXPORT_PARAMETER = 7;

    public function load(ObjectManager $manager): void
    {
        $entities = ["trainee", "course", "cohort", "trainer", "sector", "responsible", "coordinator", "internship"];
        for ($i = 0; $i < self::NB_EXPORT_PARAMETER; $i++) {
            $exportParameter = new ExportParameter();
            $exportParameter->setDtype($entities[$i]);
            $exportParameter->setField('["id"]');

            $manager->persist($exportParameter);
            $this->addReference(self::EXPORT_PARAMETER_REFERENCE_TAG . $i, $exportParameter);
        }

        $manager->flush();
    }
}
