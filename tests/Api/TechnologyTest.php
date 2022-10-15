<?php

namespace App\Tests\Api;

class TechnologyTest extends AbstractApiTestCase
{

    public function testGetTechnologiesList(): void
    {
        $token = $this->loginRequest()->toArray()['token'];

        $response = $this->createAuthorizedRequest(method: 'GET', uri: '/api/technologies', token: $token)->toArray()['hydra:member'][0];


        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('name', $response);
    }

    public function testSearchTechnology(): void
    {

        $token = $this->loginRequest()->toArray()['token'];

        $response = $this->createAuthorizedRequest(method: 'GET', uri: '/api/technologies?name=Java', token: $token)->toArray()['hydra:member'][0];


        $this->assertResponseIsSuccessful();
        $this->assertArraySubset(['name' => 'Javascript'], $response);
    }

    public function testAddNewTechnologyToUser()
    {
        $token = $this->loginRequest()->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/users/1/technologies',
            json: ['iri' => '/api/technologies/2'],
            token: $token
        );


        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['@id' => '/api/technologies/2']);
    }

    public function testSameTechnologyTwiceToUser()
    {
        $token = $this->loginRequest()->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/users/1/technologies',
            json: ['iri' => '/api/technologies/1'],
            token: $token
        );


        $this->assertResponseStatusCodeSame(400);

        $this->assertJsonContains([
            'hydra:description' => 'User has already contain this technology'
        ]);
    }

    public function testAddNewTechnologyNotOwnerAccess()
    {
        $token = $this->loginRequest()->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/users/2/technologies',
            json: ['iri' => '/api/technologies/2'],
            token: $token
        );


        $this->assertResponseStatusCodeSame(403);
    }

    public function testAddNewTechnologyNotAuthenticated()
    {

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/users/2/technologies',
            json: ['iri' => '/api/technologies/2'],
        );


        $this->assertResponseStatusCodeSame(401);
    }

    public function testAddNewTechnologyNotExistingOne()
    {
        $token = $this->loginRequest()->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/users/1/technologies',
            json: ['iri' => '/api/technologies/1000'],
            token: $token
        );


        $this->assertResponseStatusCodeSame(400);
    }

    public function testAddNewTechnologyValidatio()
    {
        $token = $this->loginRequest()->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/users/1/technologies',
            json: ['iri' => ''],
            token: $token
        );


        $this->assertResponseStatusCodeSame(400);
    }

    public function testDeleteUserTechnology(): void
    {
        $token = $this->loginRequest()->toArray()['token'];
        $this->createAuthorizedRequest(
            method: 'DELETE',
            uri: '/api/users/1/technologies/1',
            token: $token
        );

        $this->assertResponseStatusCodeSame(204);
    }

}
