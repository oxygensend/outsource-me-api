<?php

namespace App\Tests;

class TechnologyTest extends AbstractApiTestCase
{
    public function testGetTechnologiesList(): void
    {
        $token = $this->loginRequest()->toArray()['token'];

        $response = $this->createAuthorizedRequest(method:'GET',uri: '/api/technologies', token: $token)->toArray()['hydra:member'][0];


        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('name', $response);
    }

    public function testSearchTechnology(): void
    {

        $token = $this->loginRequest()->toArray()['token'];

        $response = $this->createAuthorizedRequest(method:'GET',uri: '/api/technologies?name=Java', token: $token)->toArray()['hydra:member'][0];


        $this->assertResponseIsSuccessful();
        $this->assertArraySubset(['name' => 'Javascript'], $response);
    }
}