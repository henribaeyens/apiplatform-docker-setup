<?php

declare(strict_types=1);

namespace App\Security;

use App\Exception\NotVerifiedException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
    }

    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof UserInterface) {
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
