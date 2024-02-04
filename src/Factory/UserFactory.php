<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;
use App\Entity\UserInterface;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFactory implements UserFactoryInterface
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function create(
        string $firstName,
        string $lastName,
        string $email,
        string $plainPassword,
        array $roles,
        bool $verified = false
    ): UserInterface {
        $userExists = $this->userRepository->findOneByEmail($email);

        if ($userExists instanceof UserInterface) {
            throw new \RuntimeException(sprintf('There is already a user registered with the "%s" email.', $email));
        }

        $user = new User();
        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setRoles($roles);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);
        $user->setVerified($verified);

        return $user;
    }
}
