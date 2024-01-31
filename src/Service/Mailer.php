<?php

namespace App\Service;

use Twig\Environment;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class Mailer {

    const FROM = 'Sonata Administration';
    const FROM_EMAIL = 'no-reply@docker.localhost';


    public function __construct(
        private MailerInterface $mailer,
        private UrlGeneratorInterface $urlGenerator,
        private Environment $twig,
        private TranslatorInterface $translator,
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

    public function userCreatedNotification(string $message): bool 
    {
        $email = (new Email())
            ->from('hello@example.com')
            ->to('you@example.com')
            ->subject('A new user has been created')
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

    public function sendResettingEmailMessage(UserInterface $user): bool 
    {
        /** @var UserInterface $user */
        $url = $this->urlGenerator->generate('admin_password_reset', [
            'token' => $user->getRecoveryToken(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $body = $this->twig->render('Admin/Security/Email/password_reset.html.twig', [
            'user' => $user,
            'confirmationUrl' => $url,
        ]);

        $email = (new Email())
            ->from(sprintf('%s <%s>', self::FROM, self::FROM_EMAIL))
            ->to($user->getEmail())
            ->subject($this->translator->trans('security.password_reset.request.subject'))
            ->html($body);

        try {
            $this->mailer->send($email);
            return true;
        } catch (TransportExceptionInterface $e) {
            dd($e);
            return false;
        }
    }

}