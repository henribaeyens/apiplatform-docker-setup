
<?php

use Symfony\Component\HttpFoundation\Response;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

uses(App\Tests\ApiTestCase::class);

beforeEach(function () {
    self::bootKernel();
});

it('fails to authenticate a user (wrong or non-existing email address)', function () {
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

    expect($response->getStatusCode())->toBe(Response::HTTP_UNAUTHORIZED);
});

it('fails to authenticate a non-verified user', function () {
    $payload = [
        'email' => 'user2@api.local',
        'password' => 'nottoosecret',
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

    expect($response->getStatusCode())->toBe(Response::HTTP_UNAUTHORIZED);
});

it('authenticates a verified user', function () {
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
                'accept' => ['application/json'],
                'CONTENT_TYPE' => 'application/json',
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
                'accept' => ['application/json'],
                'CONTENT_TYPE' => 'application/json',
            ]
        ]
    );

    expect($response->getStatusCode())->toBe(Response::HTTP_OK);

    $token = $response->toArray()['token'];

    $this->expectException(AccessDeniedException::class);

    $client->getKernelBrowser()->catchExceptions(false);

    $client->request(
        'GET', 
        '/' . $this->apiVersion . '/users',
        [
            'headers' => [
                'accept' => ['application/ld+json'],
                'CONTENT_TYPE' => 'application/ld+json',
                'Authorization' => 'Bearer ' . $token,
            ]
        ]
    );
});

it('registers a user, verifies it, and returns an authentication token', function () {
    $faker = Faker\Factory::create();

    $email = $faker->email();
    $firstName = $faker->firstName();
    $lastName = $faker->lastName();

    $payload = [
        'email' => $email,
        'firstName' => $firstName,
        'lastName' => $lastName,
        'password' => 'itsnosecret',
    ];

    $response = static::createClient(
        [],
        ['base_uri' => $this->baseUrl],
    )->request(
        'POST', 
        '/register',
        [
            'body' => json_encode($payload),
            'headers' => [
                'accept' => ['application/ld+json'],
                'CONTENT_TYPE' => 'application/ld+json',
            ]
        ]
    );

    expect($response->getStatusCode())->toBe(Response::HTTP_CREATED);

    $userId = $response->toArray()['id'];

    $repo = self::getContainer()->get('App\Repository\UserRepository');
    $user = $repo->find((int) $userId);

    expect($user)->toBeInstanceOf(UserInterface::class);
    expect($user->getEmailVerificationCode())->toBeString()->toHaveLength(6);

    $payload = [
        'emailVerificationCode' => $user->getEmailVerificationCode(),
    ];

    $response = static::createClient(
        [],
        ['base_uri' => $this->baseUrl],
    )->request(
        'POST', 
        '/email_verification',
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

