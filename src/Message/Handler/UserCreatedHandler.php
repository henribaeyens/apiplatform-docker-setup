<?php
namespace App\Message\Handler;

use App\Service\Mailer;
use App\Message\UserCreated as UserCreatedMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UserCreatedHandler
{
        
    public function __construct(
        private Mailer $mailer,
    )
    {
    }
    
    public function __invoke(UserCreatedMessage $message)
    {
        $this->mailer->userCreatedNotification($message->getContent());
    }
}