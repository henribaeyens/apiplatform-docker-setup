<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase as BaseApiTestCase;

class ApiTestCase extends BaseApiTestCase
{
    public string $baseUrl = "https://api.docker.localhost";
    public string $apiVersion = "v1";
}