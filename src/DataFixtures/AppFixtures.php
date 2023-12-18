<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    )
    {
    }
    public function load(ObjectManager $manager): void
    {
        $user1 = new User();
        $user1->setEmail('user1@api.local');
        $user1->setFirstName('fname1');
        $user1->setLastName('lname1');
        $user1->setRoles([UserRole::USER->value]);
        $user1->setVerified(true);

        $hashedPassword = $this->passwordHasher->hashPassword($user1, 'notsosecret');
        $user1->setPassword($hashedPassword);

        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('user2@api.local');
        $user2->setFirstName('fname2');
        $user2->setLastName('lname2');
        $user2->setRoles([UserRole::USER->value]);
        $user2->setVerified(false);

        $hashedPassword = $this->passwordHasher->hashPassword($user2, 'nottoosecret');
        $user2->setPassword($hashedPassword);

        $manager->persist($user2);

        $user3 = new User();
        $user3->setEmail('user3@api.local');
        $user3->setFirstName('fname3');
        $user3->setLastName('lname3');
        $user3->setRoles([UserRole::ADMIN->value]);
        $user3->setVerified(true);

        $hashedPassword = $this->passwordHasher->hashPassword($user3, 'notmuchofasecret');
        $user3->setPassword($hashedPassword);

        $manager->persist($user3);

        $manager->flush();
    }
}
