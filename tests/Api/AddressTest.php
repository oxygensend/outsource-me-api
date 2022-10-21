<?php

namespace App\Tests\Api;

class AddressTest extends AbstractApiTestCase
{
    public function testGetUniversityList(): void
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(method: 'GET', uri: '/api/addresses', token: $token)->toArray()['hydra:member'][0];

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('city', $response);
    }

    public function testFindAddressByPostalCode(): void
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(method: 'GET', uri: '/api/addresses?search=23-408', token: $token)->toArray()['hydra:member'][0];

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('city', $response);
    }

    public function testFindAddressByPostalCodeInValidSearchFormat(): void
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(method: 'GET', uri: '/api/addresses?search=23-40', token: $token);

        $this->assertResponseStatusCodeSame(400);
    }
}