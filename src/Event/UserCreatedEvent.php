<?php

declare(strict_types=1);

namespace App\Event;

use Doctrine\ORM\Events;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsEntityListener(event: Events::postPersist, method: 'newUserCreated', entity: User::class)]
final class UserCreatedEvent
{
    public function __construct(
    )
    {
    }

    public function newUserCreated(UserInterface $user): void
    {        
    }
}
