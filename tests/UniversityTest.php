<?php

namespace App\Tests;

class UniversityTest extends AbstractApiTestCase
{
    public function testGetUniversityList(): void
    {
        $token = $this->loginRequest()->toArray()['token'];

        $response = $this->createAuthorizedRequest(method:'GET',uri: '/api/universities', token: $token)->toArray()['hydra:member'][0];


        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('name', $response);
    }

    public function testSearchUniversity(): void
    {

        $token = $this->loginRequest()->toArray()['token'];

        $response = $this->createAuthorizedRequest(method:'GET',uri: '/api/universities?name=AGH', token: $token)->toArray()['hydra:member'][0];


        $this->assertResponseIsSuccessful();
        $this->assertArraySubset(['name' => 'AGH'], $response);
    }

}