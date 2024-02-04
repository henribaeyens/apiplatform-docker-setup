<?php

declare(strict_types=1);

namespace App\Message\Handler;

use App\Message\MailNotification;
use App\Service\Mailer;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class MailNotificationHandler
{
    public function __construct(
        private Mailer $mailer,
    ) {
    }

    public function __invoke(MailNotification $message): void
    {
        $this->mailer->sendNotification($message->getContent());
    }
}
