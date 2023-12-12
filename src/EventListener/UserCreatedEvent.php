<?php

namespace App\EventListener;

use App\Entity\User;
use App\Message\UserCreated as UserCreatedMessage;
use Doctrine\ORM\Events;
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
        $this->msgBusInterface->dispatch(new UserCreatedMessage('Welcome ' . $user->getFirstName()));
    }
}
