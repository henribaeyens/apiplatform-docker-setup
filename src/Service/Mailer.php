<?php

namespace App\Service;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class Mailer {

    public function __construct(
        private MailerInterface $mailer,
    )
    {
    }

    public function send(): bool 
    {
        $email = (new Email())
            ->from('hello@example.com')
            ->to('you@example.com')
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        try {
            $this->mailer->send($email);
            return true;
        } catch (TransportExceptionInterface $e) {
            dd($e);
            return false;
        }
    }

    public function sendNotification(string $message): bool 
    {
        $email = (new Email())
            ->from('hello@example.com')
            ->to('you@example.com')
            ->subject('New message')
            ->text($message)
            ->html($message);

        try {
            $this->mailer->send($email);
            return true;
        } catch (TransportExceptionInterface $e) {
            dd($e);
            return false;
        }
    }

}