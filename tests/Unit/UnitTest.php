<?php

use App\Service\Mailer;
use App\Message\MailNotification as MailNotificationMessage;
use Symfony\Component\HttpFoundation\Response;

uses(App\Tests\ApiTestCase::class);

beforeEach(function () {
    self::bootKernel();
});

it('sends an email', function () {
    $mailerInterface = self::getContainer()->get('Symfony\Component\Mailer\MailerInterface');
    $mailer = new Mailer($mailerInterface);
    $response = $mailer->send();
    expect($response)->toBeTrue();
});

it('sends a message to the broker', function () {
    $busInterface = self::getContainer()->get('Symfony\Component\Messenger\MessageBusInterface');
    $response = $busInterface->dispatch(new MailNotificationMessage('Notification message sent via broker'));
    expect($response)->toBeInstanceOf(Symfony\Component\Messenger\Envelope::class);
});

it('fails to authenticate a user', function () {
    $payload = [
        'email' => 'henri@somecompany.com',
        'password' => 'notsosecret',
    ];

    $response = static::createClient(
        [],
        ['base_uri' => 'https://api.docker.localhost']
    )->request(
        'POST', 
        '/authentication',
        [
            'body' => json_encode($payload),
            'headers' => [
                'accept' => ['application/ld+json'],
                'CONTENT_TYPE' => 'application/ld+json',
            ]
        ]
    );
    // authentication fails because of a wrong email address (see fixtures)
    expect($response->getStatusCode())->toBe(Response::HTTP_UNAUTHORIZED);
});

it('authenticates a user', function () {
    $payload = [
        'email' => 'user1@api.local',
        'password' => 'notsosecret',
    ];

    $response = static::createClient(
        [],
        ['base_uri' => 'https://api.docker.localhost']
    )->request(
        'POST', 
        '/authentication',
        [
            'body' => json_encode($payload),
            'headers' => [
                'accept' => ['application/ld+json'],
                'CONTENT_TYPE' => 'application/ld+json',
            ]
        ]
    );
    expect($response->getStatusCode())->toBe(Response::HTTP_OK);
});

/*
it('creates a user', function () {
    $payload = [
        'email' => 'henri@somecompany.com',
        'firstName' => 'Henri',
        'lastName' => 'Baeyens',
        'plainPassword' => 'itsnosecret',
    ];

    $response = static::createClient(
        [],
        ['base_uri' => 'https://api.docker.localhost']
    )->request(
        'POST', 
        '/api/users',
        [
            'body' => json_encode($payload),
            'headers' => [
                'accept' => ['application/ld+json'],
                'CONTENT_TYPE' => 'application/ld+json',
            ]
        ]
    );
    expect($response->getStatusCode())->toBe(Response::HTTP_CREATED);
});
*/

