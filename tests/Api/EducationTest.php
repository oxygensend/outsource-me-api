<?php

namespace App\Tests\Api;

class EducationTest extends AbstractApiTestCase
{
    public function testAddNewEducationToUser()
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/education',
            json: [
                'university' => '/api/universities/1',
                'startDate' => '2020-10-01',
                'endDate' => '2023-10-02',
                'fieldOfStudy' => 'Math',
                'grade' => 5,
                'description' => "TEST tes testset",
            ],
            token: $token
        )->toArray();


        $this->assertResponseIsSuccessful();

        $this->assertArrayHasKey('@id', $response);
        $this->assertArrayHasKey('university', $response);
        $this->assertArrayHasKey('startDate', $response);
        $this->assertArrayHasKey('endDate', $response);
        $this->assertArrayHasKey('fieldOfStudy', $response);
        $this->assertArrayHasKey('description', $response);

    }


    public function testAddNewEducationNotAuthenticated()
    {

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/education',
            json: [
                'university' => '/api/universities/1',
                'startDate' => '2020-10-01',
                'endDate' => '2023-10-02',
                'fieldOfStudy' => 'Math',
                'grade' => 5,
                'description' => "TEST tes testset",
            ]
        );

        $this->assertResponseStatusCodeSame(401);
    }

    public function testAddNewEducationValidUniIri()
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/education',
            json: [
                'university' => '/api/universities',
                'startDate' => '2020-10-01',
                'endDate' => '2023-10-02',
                'fieldOfStudy' => 'Math',
                'grade' => 5,
                'description' => "TEST tes testset",
            ],
            token: $token
        );


        $this->assertResponseStatusCodeSame(400);
    }

    public function testAddNewEducationValidDates()
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/education',
            json: [
                'university' => '/api/universities/1',
                'startDate' => 'test',
                'endDate' => 'test',
                'fieldOfStudy' => 'Math',
                'description' => "TEST tes testset",
            ],
            token: $token
        );

        $this->assertResponseStatusCodeSame(400);
    }

    public function testAddNewEducationNoStartDate()
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/education',
            json: [
                'university' => '/api/universities/1',
                'endDate' => '2015-12-12',
                'fieldOfStudy' => 'Math',
                'description' => "TEST tes testset",
            ],
            token: $token
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(["hydra:description" => "startDate: This value should not be blank."]);
    }

    public function testDeleteUserEducation(): void
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];
        $this->createAuthorizedRequest(
            method: 'DELETE',
            uri: '/api/users/1/educations/1',
            token: $token
        );

        $this->assertResponseStatusCodeSame(204);
    }

    public function testUpdateUserEducation(): void
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];

        $data = [
            'university' => '/api/universities/1',
            'fieldOfStudy' => 'Math',
            'description' => "TEST tes testset",
        ];

        $response = $this->createAuthorizedRequest(
            method: 'PATCH',
            uri: '/api/education/1
            ',
            json: $data,
            token: $token,
            headers: [
                'Content-Type' => 'application/merge-patch+json'
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains($data);
    }
}