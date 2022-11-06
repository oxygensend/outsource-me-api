<?php

namespace App\Tests\Api;

use App\Entity\Application;
use App\Entity\Notification;

class ApplicationTest extends AbstractApiTestCase
{

    public function testAddApplication(): void
    {
        $projectDir = static::getContainer()->get('kernel')->getProjectDir();

//        $file1 = new UploadedFile($projectDir . '/fixtures/files/simple_pdf.pdf', 'file1.pdf');
//        $file2 = new UploadedFile($projectDir  . '/fixtures/files/simple_pdf_1.pdf' ,'file2.pdf');
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS2)->toArray()['token'];
        $response = $this->createMultiPartRequest(
            uri: '/api/applications',
            extra: [
                'parameters' => [

                    'jobOffer' => '/api/job_offers/job-offer-test',
                    'description' => 'Test'

                ],
                'files' => [
//                    'attachments' => [$file1, $file2]
                ]

            ],
            token: $token
        );

        $this->assertEmailCount(1);
        $email = $this->getMailerMessage();
        $this->assertEmailHasHeader($email, 'subject', 'Outsource me - masz nową aplikacje na oferte X');

        $application = $this->em->getRepository(Application::class)->findBy(['description' => 'Test']);
        $notifications = $this->em->getRepository(Notification::class)->findBy(['relatedApplication' => $application]);

        $this->assertEquals(2, count($notifications));

//        $files = $em->getRepository(Attachment::class)->findBy(['A']);
        $this->assertResponseIsSuccessful();

    }

    public function testAddApplicationAsPrinciple(): void
    {

        $token = $this->loginRequest(self::PRINCIPLE_CREDENTIALS)->toArray()['token'];
        $response = $this->createMultiPartRequest(
            uri: '/api/applications',
            extra: [
                'parameters' => [

                    'jobOffer' => '/api/job_offers/job-offer-test',
                    'description' => 'Test'

                ],
                'files' => [
                ]

            ],
            token: $token
        );

        $this->assertResponseStatusCodeSame(403);

    }

    public function testApplicateTwiceForSameJobOffer(): void
    {


        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];
        $this->createMultiPartRequest(
            uri: '/api/applications',
            extra: [
                'parameters' => [

                    'jobOffer' => '/api/job_offers/job-offer-test',
                    'description' => 'Test'

                ],
                'files' => [
                ]

            ],
            token: $token
        );

        $this->assertResponseStatusCodeSame(403);

    }

    public function testGetApplicationAsCreator(): void
    {

        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];

        $this->createAuthorizedRequest(method: 'GET', uri: '/api/applications/1', token: $token);

        $this->assertResponseIsSuccessful();
    }

    public function testGetApplicationAsJobOfferCreator(): void
    {

        $token = $this->loginRequest(self::PRINCIPLE_CREDENTIALS)->toArray()['token'];

        $this->createAuthorizedRequest(method: 'GET', uri: '/api/applications/1', token: $token);

        $this->assertResponseIsSuccessful();
    }

    public function testGetApplicationAsNotLoggedUser(): void
    {

        $this->createAuthorizedRequest(method: 'GET', uri: '/api/applications/1');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetApplicationWithNoRights()
    {

        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];

        $this->createAuthorizedRequest(method: 'GET', uri: '/api/applications/2', token: $token);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testGetApplicationContent()
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'GET',
            uri: '/api/applications/1',
            token: $token
        )->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('applying_person', $response);
        $this->assertArrayHasKey('fullName', $response['applying_person']);
        $this->assertArrayHasKey('imagePath', $response['applying_person']);
        $this->assertArrayHasKey('attachments', $response);
//        $this->assertArrayHasKey('id', $response['attachments'][0]);
//        $this->assertArrayHasKey('originalName', $response['attachments'][0]);
        $this->assertArrayHasKey('description', $response);
        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('jobOffer', $response);
        $this->assertArrayHasKey('user', $response['jobOffer']);
        $this->assertArrayHasKey('name', $response['jobOffer']);
    }

    public function testDeleteApplication(): void
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'DELETE',
            uri: '/api/applications/1',
            token: $token
        );

        $this->assertResponseIsSuccessful();
    }


    public function testDeleteApplicationAccessDenied(): void
    {
        $token = $this->loginRequest(self::PRINCIPLE_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'DELETE',
            uri: '/api/applications/1',
            token: $token
        );

        $this->assertResponseStatusCodeSame(403);
    }

    public function testApplicationChangeStatus()
    {
        $token = $this->loginRequest(self::PRINCIPLE_CREDENTIALS)->toArray()['token'];

        $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/applications/1/change_status',
            json: [
                'status' => 1
            ],
            token: $token
        );

        $this->assertResponseIsSuccessful();
    }

    public function testApplicationChangeStatusNotYourJobOffer()
    {
        $token = $this->loginRequest(self::PRINCIPLE_CREDENTIALS)->toArray()['token'];

        $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/applications/2/change_status',
            json: [
                'status' => 1
            ],
            token: $token
        );


        $this->assertResponseStatusCodeSame(403);
    }

    public function testApplicationChangeStatusAsDeveloper(): void
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];

        $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/applications/1/change_status',
            json: [
                'status' => 1
            ],
            token: $token
        );


        $this->assertResponseStatusCodeSame(403);
    }

    public function testGetDeveloperApplications(): void
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'GET',
            uri: '/api/users/1/applications',
            token: $token
        )->toArray()['hydra:member'][0];


        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('jobOffer', $response);
        $this->assertArrayHasKey('createdAt', $response);
        $this->assertArrayHasKey('name', $response['jobOffer']);
    }

    public function testGetDeveloperApplicationsAsPrinciple(): void
    {
        $token = $this->loginRequest(self::PRINCIPLE_CREDENTIALS)->toArray()['token'];

        $this->createAuthorizedRequest(
            method: 'GET',
            uri: '/api/users/1/applications',
            token: $token
        );

        $this->assertResponseStatusCodeSame(403);
    }

    public function testGetDeveloperApplicationsNotYours(): void
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS2)->toArray()['token'];

        $this->createAuthorizedRequest(
            method: 'GET',
            uri: '/api/users/1/applications',
            token: $token
        );

        $this->assertResponseStatusCodeSame(403);
    }
}