<?php

namespace App\Tests\Api;

class FormOfEmploymentTest extends AbstractApiTestCase
{
    public function testGetUniversityList(): void
    {

        $response = $this->createAuthorizedRequest(method: 'GET', uri: '/api/form_of_employments')->toArray()['hydra:member'][0];


        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('name', $response);
    }
}