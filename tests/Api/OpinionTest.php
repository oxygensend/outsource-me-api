<?php

namespace App\Tests\Api;

class OpinionTest extends AbstractApiTestCase
{
    public function testGetOpinions(): void
    {

        $response = $this->createAuthorizedRequest(method: 'GET', uri: '/api/users/1/opinions',)->toArray()['hydra:member'][0];


        $this->assertResponseIsSuccessful();
        $this->assertOpinionContent($response);

    }

    public function testAddNewOpinion()
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/opinions',
            json: [
                'scale' => 5,
                'description' => 'test',
                'toWho' => '/api/users/2'
            ],
            token: $token
        )->toArray();


        $this->assertResponseIsSuccessful();
        $this->assertOpinionContent($response);

    }

    public function testAddOpinionNegativeScale(): void
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/opinions',
            json: [
                'scale' => -1,
                'toWho' => '/api/users/2'
            ],
            token: $token
        );


        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'hydra:description' => 'scale: Scale must be in range 0 to 5'
        ]);
    }

    public function testAddOpinionUpperRange()
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/opinions',
            json: [
                'scale' => 6,
                'toWho' => '/api/users/2'
            ],
            token: $token
        );


        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'hydra:description' => 'scale: Scale must be in range 0 to 5'
        ]);
    }

    public function testAddOpinionToYourself()
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/opinions',
            json: [
                'scale' => 5,
                'toWho' => '/api/users/1'
            ],
            token: $token
        );


        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'hydra:description' => 'Opinion self-assignment is forbidden'
        ]);

    }

    public function testAddOpinionTwiceToTheSameUser()
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/opinions',
            json: [
                'scale' => 5,
                'toWho' => '/api/users/3'
            ],
            token: $token
        );


        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'hydra:description' => 'You can only assign one opinion to one user'
        ]);

    }


    public function testAddNewOpinionNotAuthenticated()
    {

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/opinions',
            json: [
                'scale' => 5,
                'toWho' => '/api/users/2'
            ],
        );


        $this->assertResponseStatusCodeSame(401);
    }


    public function testDeleteOpinion(): void
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];
        $this->createAuthorizedRequest(
            method: 'DELETE',
            uri: '/api/users/1/opinions/3',
            token: $token
        );

        $this->assertResponseStatusCodeSame(204);
    }

    public function testUpdateOpinion(): void
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];
        $response = $this->createAuthorizedRequest(
            method: 'PATCH',
            uri: '/api/opinions/3',
            json: [
                'scale' => 5,
                'description' => 'test'
            ],
            token: $token,
            headers: [
                'Content-Type' => 'application/merge-patch+json'
            ]
        )->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertOpinionContent($response);
    }

    public function testUpdateOpinionNotYours(): void
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];
        $response = $this->createAuthorizedRequest(
            method: 'PATCH',
            uri: '/api/opinions/1',
            json: [
                'scale' => 5,
                'description' => 'test'
            ],
            token: $token,
            headers: [
                'Content-Type' => 'application/merge-patch+json'
            ]
        );


        $this->assertResponseStatusCodeSame(403);
    }

    private function assertOpinionContent(array $response): void
    {

        $this->assertArrayHasKey('description', $response);
        $this->assertArrayHasKey('scale', $response);
        $this->assertArrayHasKey('fromWho', $response);
        $this->assertArrayHasKey('imagePath', $response['fromWho']);
        $this->assertArrayHasKey('fullName', $response['fromWho']);
    }
}