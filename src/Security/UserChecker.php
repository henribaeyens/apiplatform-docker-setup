<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\UserInterface;
use App\Exception\NotVerifiedException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface as CoreUserInterface;

final class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(CoreUserInterface $user)
    {
    }

    public function checkPostAuth(CoreUserInterface $user)
    {
        if (!$user instanceof CoreUserInterface) {
            return;
        }

        /** @var UserInterface $user */
        if (!$user->isVerified()) {
            $ex = new NotVerifiedException('Account is not verified.');
            $ex->setUser($user);
            throw $ex;
        }
    }
}
