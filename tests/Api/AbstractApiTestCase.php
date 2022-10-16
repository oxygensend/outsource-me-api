<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Contracts\HttpClient\ResponseInterface;

class AbstractApiTestCase extends ApiTestCase
{
    use ReloadDatabaseTrait;

    protected EntityManagerInterface $em;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {

        $this->em =  static::getContainer()->get('doctrine')->getManager();

        parent::__construct($name, $data, $dataName);
    }

    protected function loginRequest(string $password = 'test123', string $email = 'test@test.com'): \Symfony\Contracts\HttpClient\ResponseInterface
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

    protected function createAuthorizedRequest(string $method,string $uri, array $json = [], string $token = '', array $headers = []): ResponseInterface
    {
       return static::createClient()->request($method, $uri, [
           'json' => $json,
           'headers' => array_merge($headers,[
               'Authorization' => 'Bearer ' . $token
           ])
       ] );
    }
}