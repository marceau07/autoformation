<?php 

namespace App\Service;

use App\Entity\Coordinator;
use App\Entity\Responsible;
use App\Entity\Trainer;

class SectorResolver
{
    public function resolve(Responsible|Coordinator|Trainer $user): ?int
    {
        if (method_exists($user, 'getSector') && $user->getSector()) {
            return $user->getSector()->getId();
        } elseif (method_exists($user, 'getResponsible') && $user->getResponsible()) {
            return $user->getResponsible()->getSector()->getId();
        } elseif (method_exists($user, 'getCoordinator') && $user->getCoordinator()) {
            return $user->getCoordinator()->getResponsible()->getSector()->getId();
        }

        return null;
    }
}