<?php

use App\Service\Mailer;
use App\Message\MailNotification;

uses(App\Tests\ApiTestCase::class);

beforeEach(function () {
    self::bootKernel();
});

it('sends an email', function () {
    $mailerInterface = self::getContainer()->get('Symfony\Component\Mailer\MailerInterface');
    $mailer = new Mailer($mailerInterface);
    $response = $mailer->send();
    $this->assertEquals(true, $response);
});

it('sends a message to the broker', function () {
    $busInterface = self::getContainer()->get('Symfony\Component\Messenger\MessageBusInterface');
    $response = $busInterface->dispatch(new MailNotification('Look! I created a message!'));
    $this->assertTrue($response instanceof Symfony\Component\Messenger\Envelope);
});

/*
it('gets services from the API', function () {
    $response = static::createClient()->request('GET', 'https://srvc.docker.localhost/api/services');
    $this->assertEquals(200, $response->getStatusCode());
  //  $this->testGetCollection();

 //   expect($response->json())->toEqual('??? what goes here ???');
});
*/