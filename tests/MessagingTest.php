<?php

namespace App\Tests;

use App\Entity\Message;
use App\Entity\Notification;
use App\Entity\User;
use App\Event\Notification\NotificationText;
use App\Tests\Api\AbstractApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class MessagingTest extends AbstractApiTestCase
{

    public function testSendEmailMessageToUser()
    {
        $token = $this->loginRequest(self::PRINCIPLE_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/messages',
            json: [
                'content' => 'Test message',
                'subject' => 'Test subject',
                'receiverIri' => '/api/users/1'
            ],
            token: $token
        );

        $this->assertEmailCount(1);
        $email = $this->getMailerMessage();
        $this->assertEmailHasHeader($email, 'subject', 'Outsource me: Test subject');

        $this->assertResponseIsSuccessful();

        $user = $this->em->getRepository(User::class)->find(1);
        $message = $this->em->getRepository(Message::class)->findOneBy(['content' => 'Test message']);
        $notification = $this->em->getRepository(Notification::class)->findOneBy([
            'receiver' => $user,
            'relatedMessage' => $message,
            'channel' => 'internal'
        ]);

        $this->assertNotNull($notification);
        $this->assertEquals(Notification::CHANNEL_INTERNAL, $notification->getChannel());
        $this->assertEquals(NotificationText::getMessageEventInternalText($message), $notification->getContent());
    }

    public function testSendEmailMessageAuthorization()
    {
        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/messages',
            json: [
                'content' => 'Test message',
                'subject' => 'Test subject',
                'receiverIri' => '/api/users/1'
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

    }


    public function testSendEmailMessageToYourself()
    {
        $token = $this->loginRequest(self::PRINCIPLE_CREDENTIALS)->toArray()['token'];

        $response = $this->createAuthorizedRequest(
            method: 'POST',
            uri: '/api/messages',
            json: [
                'content' => 'Test message',
                'subject' => 'Test subject',
                'receiverIri' => '/api/users/2'
            ],
            token: $token
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

    }
}