<?php

namespace App\Tests\Api;


use App\Entity\JobOffer;
use Symfony\Component\HttpFoundation\Response;

class JobOfferTest extends AbstractApiTestCase
{


    public function testGetJobOfferCollection(): void
    {
        $response = $this->createAuthorizedRequest(
            method: 'GET',
            uri: '/api/job_offers'
        )->toArray()['hydra:member'][0];

        $this->assertResponseIsSuccessful();
        $this->assertResponseContent($response);
    }

    public function testGetJobPagination(): void
    {
        $response = $this->createAuthorizedRequest(
            method: 'GET',
            uri: '/api/job_offers?page=1'
        )->toArray();


        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('hydra:view', $response);
    }


    public function testGetJobOfferOne(): void
    {
        $response = $this->createAuthorizedRequest(
            method: 'GET',
            uri: '/api/job_offers/1'
        )->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertResponseContent($response);
        $this->assertArrayHasKey('numberOfApplications', $response);
        $this->assertArrayHasKey('name', $response['workType'][0]);
        $this->assertArrayHasKey('name', $response['formOfEmployment']);
        $this->assertArrayHasKey('name', $response['technologies'][0]);
        $this->assertArrayHasKey('city', $response['address']);
    }
    public function testAddJobOffer(): void
    {
        $token = $this->loginRequest(self::PRINCIPLE_CREDENTIALS)->toArray()['token'];
        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/job_offers',
            json: [
                'name' => 'test job offer',
                'description' => 'test',
                'workType' => [
                    '/api/work_types/1',
                    '/api/work_types/2'
                ],
                'technologies' => [
                    '/api/technologies/1',
                    '/api/technologies/2',
                    '/api/technologies/3',
                ],
                'salaryRange' => '2000-3000zl',
                'formOfEmployment' => '/api/form_of_employments/1',
                'address' => '/api/addresses/1',
            ],
            token: $token
        )->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertResponseContent($response);

    }

    public function testAddJobOfferValidName()
    {

        $token = $this->loginRequest(self::PRINCIPLE_CREDENTIALS)->toArray()['token'];
        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/job_offers',
            json: [
                'description' => 'test',
                'workType' => [
                    '/api/work_types/1',
                    '/api/work_types/2'
                ],
                'salaryRange' => '2000-3000zl',
                'formOfEmployment' => '/api/form_of_employments/1',
                'address' => '/api/addresses/1',
            ],
            token: $token
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertJsonContains([
            'hydra:description' => 'name: This value should not be blank.'
        ]);
    }


    public function testAddJobOfferValidDescription()
    {

        $token = $this->loginRequest(self::PRINCIPLE_CREDENTIALS)->toArray()['token'];
        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/job_offers',
            json: [
                'name' => 'test',
                'workType' => [
                    '/api/work_types/1',
                    '/api/work_types/2'
                ],
                'salaryRange' => '2000-3000zl',
                'formOfEmployment' => '/api/form_of_employments/1',
                'address' => '/api/addresses/1',
            ],
            token: $token
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertJsonContains([
            'hydra:description' => 'description: This value should not be blank.'
        ]);
    }


    public function testAddJobOfferValidFormOfEmployment()
    {

        $token = $this->loginRequest(self::PRINCIPLE_CREDENTIALS)->toArray()['token'];
        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/job_offers',
            json: [
                'name' => 'test',
                'description' => 'test',
                'workType' => [
                    '/api/work_types/1',
                    '/api/work_types/2'
                ],
                'salaryRange' => '2000-3000zl',
                'address' => '/api/addresses/1',
            ],
            token: $token
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertJsonContains([
            'hydra:description' => 'formOfEmployment: This value should not be blank.'
        ]);
    }

    public function testAddJobOfferNotPrinciple(): void
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];
        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/job_offers',
            json: [
            ],
            token: $token
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

    }

    public function testAddJobOfferNotAuthorized(): void
    {
        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/job_offers',
            json: [
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

    }

    public function testUpdateJobPosition(): void
    {
        $token = $this->loginRequest(self::PRINCIPLE_CREDENTIALS)->toArray()['token'];
        $response = $this->createAuthorizedRequest(
            method: 'PATCH',
            uri: '/api/job_offers/1',
            json: [
                'name' => 'test job offer',
                'description' => 'test',
                'workType' => [
                    '/api/work_types/1',
                    '/api/work_types/2'
                ],
                'salaryRange' => '2000-3000zl',
                'formOfEmployment' => '/api/form_of_employments/1',
                'address' => '/api/addresses/1',
            ],
            token: $token,
            headers: [
                'Content-Type' => 'application/merge-patch+json'
            ]
        );


        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'name' => 'test job offer',
            'description' => 'test',
            'salaryRange' => '2000-3000zl',
        ]);

    }

    public function testUpdateJobOfferValidName()
    {

        $token = $this->loginRequest(self::PRINCIPLE_CREDENTIALS)->toArray()['token'];
        $response = $this->createAuthorizedRequest(
            method: 'PATCH',
            uri: '/api/job_offers/1',
            json: [
                'name' => '',
                'description' => 'test',
                'workType' => [
                    '/api/work_types/1',
                    '/api/work_types/2'
                ],
                'salaryRange' => '2000-3000zl',
                'formOfEmployment' => '/api/form_of_employments/1',
                'address' => '/api/addresses/1',
            ],
            token: $token,
            headers: [
                'Content-Type' => 'application/merge-patch+json'
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertJsonContains([
            'hydra:description' => 'name: This value should not be blank.'
        ]);
    }


    public function testUpdateOfferValidDescription()
    {

        $token = $this->loginRequest(self::PRINCIPLE_CREDENTIALS)->toArray()['token'];
        $response = $this->createAuthorizedRequest(
            method: 'PATCH',
            uri: '/api/job_offers/1',
            json: [
                'name' => 'test',
                'description' => '',
                'workType' => [
                    '/api/work_types/1',
                    '/api/work_types/2'
                ],
                'salaryRange' => '2000-3000zl',
                'formOfEmployment' => '/api/form_of_employments/1',
                'address' => '/api/addresses/1',
            ],
            token: $token,
            headers: [
                'Content-Type' => 'application/merge-patch+json'
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertJsonContains([
            'hydra:description' => 'description: This value should not be blank.'
        ]);
    }


    public function testUpdateJobOfferValidFormOfEmployment()
    {

        $token = $this->loginRequest(self::PRINCIPLE_CREDENTIALS)->toArray()['token'];
        $response = $this->createAuthorizedRequest(
            method: 'PATCH',
            uri: '/api/job_offers/1',
            json: [
                'name' => 'test',
                'description' => 'test',
                'workType' => [
                    '/api/work_types/1',
                    '/api/work_types/2'
                ],
                'formOfEmployment' => '',
                'salaryRange' => '2000-3000zl',
                'address' => '/api/addresses/1',
            ],
            token: $token,
            headers: [
                'Content-Type' => 'application/merge-patch+json'
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

    }

    public function testUpdateJobOfferNotPrinciple(): void
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];
        $response = $this->createAuthorizedRequest(
            method: 'PATCH',
            uri: '/api/job_offers/1',
            json: [
            ],
            token: $token,
            headers: [
                'Content-Type' => 'application/merge-patch+json'
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

    }

    public function testUdpdateJobOfferNotAuthenticated()
    {

        $response = $this->createAuthorizedRequest(
            method: 'PATCH',
            uri: '/api/job_offers/1',
            json: [
            ],
            headers: [
                'Content-Type' => 'application/merge-patch+json'
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

    }


    public function testUpdateJobOfferUserIsNotOwner()
    {
        $token = $this->loginRequest(self::PRINCIPLE_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'PATCH',
            uri: '/api/job_offers/2',
            json: [
            ],
            token: $token, headers: [
            'Content-Type' => 'application/merge-patch+json'
        ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

    }

    public function testDeleteJobOfferIsSoft()
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        $jobOfferBefore = $em->getRepository(JobOffer::class)->find(1);

        $token = $this->loginRequest(self::PRINCIPLE_CREDENTIALS)->toArray()['token'];
        $response = $this->createAuthorizedRequest(
            method: 'DELETE',
            uri: '/api/job_offers/1',
            token: $token

        );

        $jobOfferAfter = $em->getRepository(JobOffer::class)->find(1);

        $this->assertFalse($jobOfferBefore->isArchived());
        $this->assertTrue($jobOfferAfter->isArchived());
    }


    public function testDeleteJobOffer(): void
    {
        $token = $this->loginRequest(self::PRINCIPLE_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'DELETE',
            uri: '/api/job_offers/1',
            json: [
            ],
            token: $token
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testDeleteJobOfferUserIsNotOwner(): void
    {
        $token = $this->loginRequest(self::PRINCIPLE_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'DELETE',
            uri: '/api/job_offers/2',
            json: [
            ],
            token: $token
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testDeleteJobOfferNotAuthorizated(): void
    {

        $response = $this->createAuthorizedRequest(
            method: 'DELETE',
            uri: '/api/job_offers/1',
            json: [
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }


    private function assertResponseContent(array $response): void
    {
        $this->assertArrayHasKey('@id', $response);
        $this->assertArrayHasKey('name', $response);
        $this->assertArrayHasKey('description', $response);
        $this->assertArrayHasKey('shortDescription', $response);
        $this->assertArrayHasKey('salaryRange', $response);
        $this->assertArrayHasKey('user', $response);
        $this->assertArrayHasKey('fullName', $response['user']);
        $this->assertArrayHasKey('imagePath', $response['user']);
        $this->assertArrayHasKey('city', $response['address']);
        $this->assertArrayHasKey('numberOfApplications', $response);
    }

}