<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\UserInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Exception\InvalidArgumentException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class Mailer
{
    public const FROM = 'API';
    public const FROM_ADMIN = 'API Admin';
    public const FROM_EMAIL = 'no-reply@docker.localhost';

    public function __construct(
        private MailerInterface $mailer,
        private UrlGeneratorInterface $urlGenerator,
        private Environment $twig,
        private TranslatorInterface $translator,
    ) {
    }

    public function send(): bool
    {
        $email = (new Email())
            ->from(sprintf('%s <%s>', self::FROM, self::FROM_EMAIL))
            ->to('you@example.com')
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        try {
            $this->mailer->send($email);

            return true;
        } catch (TransportExceptionInterface $e) {
            return false;
        }
    }

    public function sendNotification(string $message): bool
    {
        $email = (new Email())
            ->from(sprintf('%s <%s>', self::FROM, self::FROM_EMAIL))
            ->to('you@example.com')
            ->subject('New message')
            ->text($message)
            ->html($message);

        try {
            $this->mailer->send($email);

            return true;
        } catch (TransportExceptionInterface $e) {
            return false;
        }
    }

    public function sendUserVWelcomeMessage(UserInterface $user): bool
    {
        try {
            $to = Address::create(sprintf('%s %s <%s>', $user->getFirstName(), $user->getLastName(), $user->getEmail()));
        } catch (InvalidArgumentException $e) {
            return false;
        }
        $email = (new Email())
            ->from(sprintf('%s <%s>', self::FROM, self::FROM_EMAIL))
            ->to($to)
            ->subject(sprintf('Welcome %s!', $user->getFirstName()))
            ->html('Some welcome blah blah blah');

        try {
            $this->mailer->send($email);

            return true;
        } catch (TransportExceptionInterface $e) {
            return false;
        }
    }

    public function sendUserVerifiedNotification(UserInterface $user): bool
    {
        $email = (new Email())
           ->from(sprintf('%s <%s>', self::FROM, self::FROM_EMAIL))
           ->to('you@example.com')
           ->subject('A new user has been verified')
           ->html(sprintf('User %s has been verified!', $user->__toString()));

        try {
            $this->mailer->send($email);

            return true;
        } catch (TransportExceptionInterface $e) {
            return false;
        }
    }

    public function sendEmailVerificationCode(UserInterface $user): bool
    {
        try {
            $to = Address::create(sprintf('%s %s <%s>', $user->getFirstName(), $user->getLastName(), $user->getEmail()));
        } catch (InvalidArgumentException $e) {
            return false;
        }
        $body = $this->twig->render('Registration/Email/email_verification_code.html.twig', [
            'user' => $user,
        ]);

        $email = (new Email())
            ->from(sprintf('%s <%s>', self::FROM, self::FROM_EMAIL))
            ->to($to)
            ->subject($this->translator->trans('email.subject.email_verification'))
            ->html($body);

        try {
            $this->mailer->send($email);

            return true;
        } catch (TransportExceptionInterface $e) {
            return false;
        }
    }

    public function sendResettingEmailMessage(UserInterface $user): bool
    {
        try {
            $to = Address::create(sprintf('%s %s <%s>', $user->getFirstName(), $user->getLastName(), $user->getEmail()));
        } catch (InvalidArgumentException $e) {
            return false;
        }
        $url = $this->urlGenerator->generate('admin_password_reset', [
            'token' => $user->getRecoveryToken(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $body = $this->twig->render('Admin/Security/Email/password_reset.html.twig', [
            'user' => $user,
            'confirmationUrl' => $url,
        ]);

        $email = (new Email())
            ->from(sprintf('%s <%s>', self::FROM_ADMIN, self::FROM_EMAIL))
            ->to($to)
            ->subject($this->translator->trans('email.subject.password_reset'))
            ->html($body);

        try {
            $this->mailer->send($email);

            return true;
        } catch (TransportExceptionInterface $e) {
            return false;
        }
    }
}
