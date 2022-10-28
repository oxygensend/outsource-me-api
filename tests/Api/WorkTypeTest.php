<?php

namespace App\Tests\Api;

class WorkTypeTest extends AbstractApiTestCase
{
    public function testGetUniversityList(): void
    {

        $response = $this->createAuthorizedRequest(method: 'GET', uri: '/api/work_types')->toArray()['hydra:member'][0];


        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('name', $response);
    }
}