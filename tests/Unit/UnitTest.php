<?php

use App\Service\Mailer;
use Symfony\Component\HttpFoundation\Response;
use App\Message\MailNotification as MailNotificationMessage;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;

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
        ['base_uri' => $this->baseUrl],
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
        ['base_uri' => $this->baseUrl],
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
    expect($response->toArray())->toHaveKey('token');
});

it('authenticates (should succeed) and get a list of all users (should succeed in failing: only ADMINs can)', function () {    
    $payload = [
        'email' => 'user1@api.local',
        'password' => 'notsosecret',
    ];

    $client = static::createClient(
        [],
        ['base_uri' => $this->baseUrl],
    );

    $response = $client->request(
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

    $token = $response->toArray()['token'];

    $this->expectException(AccessDeniedException::class);

    $client->getKernelBrowser()->catchExceptions(false);

    $client->request(
        'GET', 
        '/api/users',
        [
            'headers' => [
                'accept' => ['application/ld+json'],
                'CONTENT_TYPE' => 'application/ld+json',
                'Authorization' => 'Bearer ' . $token,
            ]
        ]
    );
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

