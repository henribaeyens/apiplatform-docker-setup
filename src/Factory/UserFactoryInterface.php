<?php

declare(strict_types=1);

namespace App\Factory;

use Symfony\Component\Security\Core\User\UserInterface;

interface UserFactoryInterface
{
    public function create(
        string $firstName,
        string $lastName,
        string $email,
        string $plainPassword,
        array $roles,
        bool $verified = false
    ): UserInterface;
}
