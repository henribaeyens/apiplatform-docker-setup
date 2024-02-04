<?php

declare(strict_types=1);

namespace App\Message\Handler;

use App\Message\UserCreated as UserCreatedMessage;
// use App\Service\Mailer;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UserCreatedHandler
{
    public function __construct(
        // private Mailer $mailer,
    ) {
    }

    public function __invoke(UserCreatedMessage $message): void
    {
        // $this->mailer->userCreatedNotification($message->getContent());
    }
}
