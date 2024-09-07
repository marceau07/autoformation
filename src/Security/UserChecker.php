<?php

// src/Security/UserChecker.php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;  // Import your User entity
use Symfony\Contracts\Translation\TranslatorInterface;

class UserChecker implements UserCheckerInterface
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->isActivated()) {
            $message = $this->translator->trans('account.not_activated');
            throw new CustomUserMessageAccountStatusException($message);
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Cast the UserInterface to your User entity
        if (!$user instanceof User) {
            return;
        }

        // Further post-authentication checks if necessary
    }
}
