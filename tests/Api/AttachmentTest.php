<?php

namespace App\Tests\Api;


class AttachmentTest extends AbstractApiTestCase
{


    public function testDownloadAttachmentNotCreator(): void
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS2)->toArray()['token'];
        $response = $this->createAuthorizedRequest(
            method: 'GET',
            uri: '/api/attachments/1',
            token: $token
        );

        self::assertResponseStatusCodeSame(403);
    }


    public function testDownloadAttachmentNotYourJobOffer(): void
    {
        $token = $this->loginRequest(self::PRINCIPLE_CREDENTIALS)->toArray()['token'];
        $response = $this->createAuthorizedRequest(
            method: 'GET',
            uri: '/api/attachments/2',
            token: $token
        );

        self::assertResponseStatusCodeSame(403);
    }

    public function testDownloadAttachmentAsCreator(): void
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];
        $response = $this->createAuthorizedRequest(
            method: 'GET',
            uri: '/api/attachments/1',
            token: $token
        );

        $this->assertResponseIsSuccessful();
    }

    public function testDownloadAttachmentAsJobOfferCreator(): void
    {
        $token = $this->loginRequest(self::PRINCIPLE_CREDENTIALS)->toArray()['token'];
        $response = $this->createAuthorizedRequest(
            method: 'GET',
            uri: '/api/attachments/1',
            token: $token
        );

        $this->assertResponseIsSuccessful();
    }

}