<?php

namespace App\Tests\Api;

use App\Entity\JobOffer;
use App\Entity\Notification;
use Symfony\Component\HttpFoundation\Response;

class NotificationTest extends AbstractApiTestCase
{
    public function testGetNotifications(): void
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'GET',
            uri: '/api/users/1/notifications',
            token: $token
        )->toArray()['hydra:member'][0];

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('content', $response);
        $this->assertArrayHasKey('displayedAt', $response);
    }

    public function testGetNotYourNotifications(): void
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'GET',
            uri: '/api/users/2/notifications',
            token: $token
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

    }

    public function testMarkNotificationSeen(): void
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/users/1/notifications/1/mark_seen',
            token: $token
        )->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('displayedAt', $response);
    }


    public function testMarkNotYoursNotificationSeen(): void
    {
        $token = $this->loginRequest(self::PRINCIPLE_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/users/1/notifications/1/mark_seen',
            token: $token
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testDeleteNotification(): void
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        $notificationBefore = $em->getRepository(Notification::class)->find(1);

        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'DELETE',
            uri: '/api/notifications/1',
            token: $token
        );

        $notificationAfter = $em->getRepository(Notification::class)->find(1);
        $this->assertResponseIsSuccessful();

        $this->assertFalse($notificationBefore->isDeleted());
        $this->assertTrue($notificationAfter->isDeleted());
    }

    public function testDeleteNotificationNotYours(): void
    {
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'DELETE',
            uri: '/api/notifications/1',
            token: $token
        );

        $this->assertResponseIsSuccessful();
    }
}