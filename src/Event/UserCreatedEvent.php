<?php

namespace App\Event;

use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\ORM\Events;
use App\Message\UserCreated as UserCreatedMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;

#[AsEntityListener(event: Events::postPersist, method: 'newUserCreated', entity: User::class)]
final class UserCreatedEvent
{
    public function __construct(
        private MessageBusInterface $msgBusInterface,
    )
    {
    }

    public function newUserCreated(User $user): void
    {        
        if (!$user->hasRole(UserRole::ADMIN->value)) {
            $this->msgBusInterface->dispatch(new UserCreatedMessage('Welcome ' . $user->getFirstName()));
        }
    }
}
