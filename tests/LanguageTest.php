<?php

namespace App\Tests;

use App\Tests\Api\AbstractApiTestCase;

class LanguageTest extends AbstractApiTestCase
{

    public function testGetUsersLanguages(): void
    {
        $token = $this->loginRequest()->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'GET',
            uri: '/api/users/1/languages',
            token: $token
        )->toArray()['hydra:member'][0];

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('name', $response);
        $this->assertArrayHasKey('description', $response);
    }


    public function testAddNewLanguage(): void
    {

        $token = $this->loginRequest()->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/languages',
            json: [
                'name' => 'test_language',
                'description' => 'test',
                'user' => '/api/users/1'
            ],
            token: $token
        );

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            'name' => 'test_language',
            'description' => 'test'
        ]);
    }

    public function testAddNewLanguageToNotLoggedUser(): void
    {

        $token = $this->loginRequest()->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/languages',
            json: [
                'name' => 'test_language',
                'description' => 'test',
                'user' => '/api/users/2'
            ],
            token: $token
        );

        $this->assertResponseStatusCodeSame(403);

    }


    public function testAddNewLanguageNotAuthenticatedUser(): void
    {

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/languages',
            json: [
                'name' => 'test_language',
                'description' => 'test',
                'user' => '/api/users/1'
            ],
        );

        $this->assertResponseStatusCodeSame(401);
    }

    public function testAddNewLanguageValidName(): void
    {

        $token = $this->loginRequest()->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/languages',
            json: [
                'description' => 'test',
                'user' => '/api/users/1'
            ],
            token: $token
        );

        $this->assertResponseStatusCodeSame(422);

    }

    public function testUpdateUserLanguage(): void
    {
        $token = $this->loginRequest()->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'PATCH',
            uri: '/api/languages/1',
            json: [
                'name' => 'test',
                'description' => 'test',
            ],
            token: $token,
            headers: [
                'Content-Type' => 'application/merge-patch+json'
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'name' => 'test',
            'description' => 'test'
        ]);

    }

    public function testUpdateNotLoggedUserLanguage(): void
    {
        $token = $this->loginRequest()->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'PATCH',
            uri: '/api/languages/2',
            json: [
                'name' => 'test',
                'description' => 'test',
            ],
            token: $token,
            headers: [
                'Content-Type' => 'application/merge-patch+json'
            ]
        );

        $this->assertResponseStatusCodeSame(403);

    }


    public function testDeleteLanguageFromUser(): void
    {

        $token = $this->loginRequest()->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'DELETE',
            uri: '/api/users/1/languages/1',
            token: $token
        );

        $this->assertResponseStatusCodeSame(204);
    }



}