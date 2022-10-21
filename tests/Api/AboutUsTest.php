<?php

namespace App\Tests\Api;


class AboutUsTest extends AbstractApiTestCase
{

    public function testAboutUsResponseContent()
    {
        $response = static::createClient()->request('GET','/api/about_us')->toArray()['hydra:member'][0];

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('title', $response);
        $this->assertArrayHasKey('mainImagePath', $response);
        $this->assertArrayHasKey('description', $response);
    }

}