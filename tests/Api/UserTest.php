<?php

namespace App\Tests\Api;

use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserTest extends AbstractApiTestCase
{

    use ReloadDatabaseTrait;

    public function testGetUserProfileUnauthorizatedUser(): void
    {
        static::createClient()->request('GET', '/api/users/1');

        $this->assertResponseStatusCodeSame(200);

    }

    public function testGetUserProfileDeveloperValidResponse(): void
    {

        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];
        $response = $this->createAuthorizedRequest(method: 'GET', uri: '/api/users/1', token: $token)->toArray();

        $this->developerAssertions($response);

    }


    public function testGetUserProfilePrincipleValid(): void
    {

        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];
        $response = $this->createAuthorizedRequest(method: 'GET', uri: '/api/users/2', token: $token)->toArray();

        $this->principleAssertions($response);

    }

    public function testGetLoggedUserData(): void
    {

        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];
        $response = $this->createAuthorizedRequest(method: 'GET', uri: '/api/users/me', token: $token)->toArray();


        $this->developerAssertions($response);

    }

    public function testUpdateUserDataNotAuthenticated(): void
    {

        $response = $this->createAuthorizedRequest(method: 'PATCH', uri: '/api/users/me');

        $this->assertResponseStatusCodeSame(401);
    }


    public function testUpdateUserDataNotOwner(): void
    {

        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];
        $response = $this->createAuthorizedRequest(method: 'PATCH', uri: '/api/users/3', token: $token, headers: [
            'Content-Type' => 'application/merge-patch+json'
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testUpdateUserData(): void
    {

        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];
        $response = $this->createAuthorizedRequest(method: 'PATCH', uri: '/api/users/me', json: [
            'email' => 'test@new.com',
            'phoneNumber' => '123321123',
            'name' => 'Anmkkl'
        ], token: $token, headers: [
            'Content-Type' => 'application/merge-patch+json'
        ])->getContent();

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'email' => 'test@new.com',
            'phoneNumber' => '123321123',
            'name' => 'Anmkkl'
        ]);

    }

    public function testGetDevelopers(): void
    {
        $response = $this->createAuthorizedRequest(method: 'GET', uri: '/api/users')->toArray()['hydra:member'][0];


        $this->assertResponseIsSuccessful();

        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('description', $response);
        $this->assertArrayHasKey('imagePath', $response);
        $this->assertArrayHasKey('fullName', $response);
        $this->assertArrayHasKey('address', $response);
        $this->assertArrayHasKey('city', $response['address']);
    }

    public function testUploadUserPhoto(): void
    {
        $projectDir = static::getContainer()->get('kernel')->getProjectDir();

////        $file = new UploadedFile($projectDir . '/tests/files/image.jpg', 'image.jpg');
//        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS2)->toArray()['token'];
//        $response = $this->createMultiPartRequest(
//            uri: '/api/users/1/upload_photo',
//            extra: [
//                'files' => [
//                    'file' => $file
//                ]
//
//            ],
//            token: $token
//        );



//        $files = $em->getRepository(Attachment::class)->findBy(['A']);
//        $this->assertResponseIsSuccessful();

    }

    private function developerAssertions(array $response): void
    {
        $this->assertResponseIsSuccessful();

        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('name', $response);
        $this->assertArrayHasKey('email', $response);
        $this->assertArrayHasKey('name', $response);
        $this->assertArrayHasKey('surname', $response);
        $this->assertArrayHasKey('phoneNumber', $response);
        $this->assertArrayHasKey('description', $response);
        $this->assertArrayHasKey('linkedinUrl', $response);
        $this->assertArrayHasKey('dateOfBirth', $response);
        $this->assertArrayHasKey('accountType', $response);
        $this->assertArrayHasKey('imagePath', $response);
        $this->assertArrayHasKey('fullName', $response);
        $this->assertArrayHasKey('address', $response);
        $this->assertArrayHasKey('lookingForJob', $response);
        $this->assertArrayHasKey('experience', $response);
        $this->assertArrayHasKey('city', $response['address']);
    }

    private function principleAssertions(array $response): void
    {
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('name', $response);
        $this->assertArrayHasKey('email', $response);
        $this->assertArrayHasKey('name', $response);
        $this->assertArrayHasKey('surname', $response);
        $this->assertArrayHasKey('phoneNumber', $response);
        $this->assertArrayHasKey('description', $response);
        $this->assertArrayHasKey('linkedinUrl', $response);
        $this->assertArrayHasKey('dateOfBirth', $response);
        $this->assertArrayHasKey('accountType', $response);
        $this->assertArrayHasKey('imagePath', $response);
        $this->assertArrayHasKey('fullName', $response);
        $this->assertArrayHasKey('address', $response);
        $this->assertArrayHasKey('city', $response['address']);
        $this->assertArrayHasKey('jobOffers', $response['']);
    }


}