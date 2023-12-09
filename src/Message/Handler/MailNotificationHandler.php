<?php
namespace App\Message\Handler;

use App\Service\Mailer;
use App\Message\MailNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class MailNotificationHandler
{
        
    public function __construct(
        private Mailer $mailer,
    )
    {
    }
    
    public function __invoke(MailNotification $message)
    {
        $this->mailer->sendNotification($message->getContent());

    }
}