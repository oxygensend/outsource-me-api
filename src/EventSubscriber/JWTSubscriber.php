<?php

namespace App\EventSubscriber;

use App\Entity\User;
use JetBrains\PhpStorm\ArrayShape;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JWTSubscriber implements EventSubscriberInterface
{

    #[ArrayShape([Events::JWT_CREATED => "string"])] public static function getSubscribedEvents(): array
    {
        return [
            Events::JWT_CREATED => 'onJWTCreated'
        ];
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {

        /** @var User $user */
        $user = $event->getUser();
        $payload = $event->getData();
        $payload['id'] = $user->getId();
        $payload['name'] = $user->getName();
        $payload['surname'] = $user->getSurname();
        $payload['fullname'] = $user->getFullName();
        $payload['accountType'] = $user->getAccountType();
        $payload['thumbnail'] = $user->getImagePathSmall();


        $event->setData($payload);

    }


}
