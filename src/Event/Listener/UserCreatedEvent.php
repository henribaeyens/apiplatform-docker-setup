<?php

declare(strict_types=1);

namespace App\Event\Listener;

use App\Entity\User;
use App\Entity\UserInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, method: 'newUserCreated', entity: User::class)]
final class UserCreatedEvent
{
    public function __construct(
    ) {
    }

    public function newUserCreated(UserInterface $user): void
    {
    }
}
