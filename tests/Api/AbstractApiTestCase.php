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

    public const PRINCIPLE_CREDENTIALS = ['email' => 'principle@test.com', 'password' => 'test123'];
    public const DEVELOPER_CREDENTIALS = ['email' => 'test@test.com', 'password' => 'test123'];
    public const DEVELOPER_CREDENTIALS2 = ['email' => 'test2@test.com', 'password' => 'test123'];

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {

        $this->em =  static::getContainer()->get('doctrine')->getManager();

        parent::__construct($name, $data, $dataName);
    }

    protected function loginRequest(array $credentials = []): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        $client = static::createClient();

        return $client->request('POST', '/api/login', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => $credentials
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

    protected function createMultiPartRequest(string $uri, array $extra = [], string $token = '') {
       return static::createClient()->request('POST', $uri,[
           'extra' => $extra,
           'headers' => [
               'Authorization' => 'Bearer ' . $token,
               'Content-Type' => 'multipart/form-data'
           ],
       ]);
    }
}