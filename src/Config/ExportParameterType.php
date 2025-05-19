<?php

namespace App\Config;

use App\Entity\Cohort;
use App\Entity\Coordinator;
use App\Entity\Internship;
use App\Entity\Prospect;
use App\Entity\Responsible;
use App\Entity\Trainee;
use App\Entity\Trainer;
use App\Entity\User;
use ReflectionClass;

enum ExportParameterType: string
{
    case USER = 'user';
    case TRAINER = 'trainer';
    case TRAINEE = 'trainee';
    case COORDINATOR = 'coordinator';
    case RESPONSIBLE = 'responsible';
    case COHORT = 'cohort';
    case PROSPECT = 'prospect';
    case INTERNSHIP = 'internship';

    public function getEntityProperties()
    {
        $obj = null;
        switch ($this) {
            case self::USER:
                $obj = new User();
            case self::TRAINER:
                $obj = new Trainer();
            case self::TRAINEE:
                $obj = new Trainee();
            case self::COORDINATOR:
                $obj = new Coordinator();
            case self::RESPONSIBLE:
                $obj = new Responsible();
            case self::COHORT:
                $obj = new Cohort();
            case self::PROSPECT:
                $obj = new Prospect();
            case self::INTERNSHIP:
                $obj = new Internship();
        }

        $reflection = new ReflectionClass($obj);
        return array_map(function ($property) {
            return $property->getName();
        }, $reflection->getProperties());
    }
}
