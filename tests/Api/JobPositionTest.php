<?php

namespace App\Tests\Api;

class JobPositionTest extends AbstractApiTestCase
{
    public function testAddNewJobPositionToUser()
    {
        $token = $this->loginRequest()->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/job_positions',
            json: [
                'name' => 'test',
                'validFrom' => '2020-10-01',
                'validTo' => '2023-10-02',
                'formOfEmployment' => '/api/form_of_employments/1',
                'description' => "TEST tes testset",
                'company' => [
                    'name' => 'AGH'
                ],
            ],
            token: $token
        )->toArray();


        $this->assertResponseIsSuccessful();

        $this->assertArrayHasKey('@id', $response);
        $this->assertArrayHasKey('company', $response);
        $this->assertArrayHasKey('validTo', $response);
        $this->assertArrayHasKey('validFrom', $response);
        $this->assertArrayHasKey('name', $response);
        $this->assertArrayHasKey('description', $response);
        $this->assertArrayHasKey('formOfEmployment', $response);

    }


    public function testAddNewJobNotAuthenticated()
    {

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/job_positions',
            json: [
                'name' => 'test',
                'validFrom' => '2020-10-01',
                'validTo' => '2023-10-02',
                'formOfEmployment' => '/api/form_of_employments/1',
                'description' => "TEST tes testset",
                'company' => [
                    'name' => 'AGH'
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(401);
    }

    public function testAddNewJobPositionValidFormOfEmplymentIri()
    {
        $token = $this->loginRequest()->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/job_positions',
            json: [
                'name' => 'test',
                'validFrom' => '2020-10-01',
                'validTo' => '2023-10-02',
                'formOfEmployment' => '/api/form_of_employments',
                'description' => "TEST tes testset",
                'company' => [
                    'name' => 'AGH'
                ],
            ],
            token: $token
        );


        $this->assertResponseStatusCodeSame(400);
    }

    public function testAddNewJobPositionValidDates()
    {
        $token = $this->loginRequest()->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/job_positions',
            json: [
                'name' => 'test',
                'validFrom' => 'test',
                'validTo' => 'test',
                'formOfEmployment' => '/api/form_of_employments/1',
                'description' => "TEST tes testset",
                'company' => [
                    'name' => 'AGH'
                ],
            ],
            token: $token
        );

        $this->assertResponseStatusCodeSame(400);
    }


    public function testDeleteUserJobPosition(): void
    {
        $token = $this->loginRequest()->toArray()['token'];
        $this->createAuthorizedRequest(
            method: 'DELETE',
            uri: '/api/users/1/job_positions/1',
            token: $token
        );

        $this->assertResponseStatusCodeSame(204);
    }

    public function testUpdateUserJobPosition(): void
    {
        $token = $this->loginRequest()->toArray()['token'];

        $data = [
            'name' => 'test2',
            'formOfEmployment' => '/api/form_of_employments/2',
            'description' => "TEST tes testset",
        ];

        $response = $this->createAuthorizedRequest(
            method: 'PATCH',
            uri: '/api/job_positions/1
            ',
            json: $data,
            token: $token,
            headers: [
                'Content-Type' => 'application/merge-patch+json'
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(
            [
                'name' => 'test2',
                'description' => 'TEST tes testset',
            ]
        );
    }
}