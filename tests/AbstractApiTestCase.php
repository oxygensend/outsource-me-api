<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;

class AbstractApiTestCase extends ApiTestCase
{

    protected EntityManagerInterface $em;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {

        $this->em =  static::getContainer()->get('doctrine')->getManager();

        parent::__construct($name, $data, $dataName);
    }

    private function loginRequest(string $password = 'test123', string $email = 'test@test.com'): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        $client = static::createClient();

        return $client->request('POST', '/api/login', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => $email,
                'password' => $password
            ]
        ]);

    }
}