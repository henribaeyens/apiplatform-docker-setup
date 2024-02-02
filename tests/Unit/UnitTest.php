<?php

use App\Service\Mailer;
use App\Message\MailNotification as MailNotificationMessage;

uses(App\Tests\TestCase::class);

it('sends an email', function () {
    $mailerInterface = self::getContainer()->get('mailer.mailer');
    $urlGenerator = self::getContainer()->get('router');
    $twig = self::getContainer()->get('twig');
    $translator = self::getContainer()->get('translator.data_collector');
    $mailer = new Mailer(
        $mailerInterface,
        $urlGenerator,
        $twig,
        $translator
    );
    $response = $mailer->send();

    expect($response)->toBeTrue();
});

it('sends a message to the broker', function () {
    $busInterface = self::getContainer()->get('messenger.bus.default');
    $response = $busInterface->dispatch(new MailNotificationMessage('Notification message sent via broker'));

    expect($response)->toBeInstanceOf(Symfony\Component\Messenger\Envelope::class);
});

