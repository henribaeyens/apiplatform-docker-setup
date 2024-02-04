<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\UserInterface;
use App\Enum\UserRole;
use App\Factory\UserFactoryInterface;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserFactoryInterface $userFactory,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        /** @var UserInterface $user */
        $user = $this->userFactory->create(
            firstName: 'fname1',
            lastName: 'lname1',
            email: 'user1@api.local',
            plainPassword: 'notsosecret',
            roles: [UserRole::USER->value],
            verified: true
        );
        $this->userRepository->save($user);

        /** @var UserInterface $user */
        $user = $this->userFactory->create(
            firstName: 'fname2',
            lastName: 'lname3',
            email: 'user2@api.local',
            plainPassword: 'nottoosecret',
            roles: [UserRole::USER->value],
        );
        $this->userRepository->save($user);
    }
}
