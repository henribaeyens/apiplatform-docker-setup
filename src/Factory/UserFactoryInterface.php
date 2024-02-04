<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\UserInterface;

interface UserFactoryInterface
{
    /**
     * @param array<string> $roles
     */
    public function create(
        string $firstName,
        string $lastName,
        string $email,
        string $plainPassword,
        array $roles,
        bool $verified = false
    ): UserInterface;
}
