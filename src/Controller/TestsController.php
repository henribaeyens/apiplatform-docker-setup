<?php

namespace App\Controller;

use App\Service\Mailer;
use App\Message\MailNotification;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TestsController extends AbstractController
{
    #[Route('/email')]
    public function sendEmail(Mailer $mailer): Response
    {
        $email = (new Email())
            ->from('hello@example.com')
            ->to('you@example.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);

        return new Response('', 200);

    }

    #[Route('/notif')]
    public function index(MessageBusInterface $bus): Response
    {
        $bus->dispatch(new MailNotification('Look! I created a message!'));

        return new Response('', 200);
    }
}