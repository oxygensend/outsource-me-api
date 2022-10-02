<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserSubscriber implements EventSubscriberInterface
{

    public function __construct(private readonly TokenStorageInterface $tokenStorage)
    {
    }


    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['resolveMe', EventPriorities::PRE_READ],
        ];
    }

    public function resolveMe(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if('_api_/users/{id}.{_format}_get' !== $request->attributes->get('_route')){
            return;
        }

        if('me' !== $request->attributes->get('id')){
            return;
        }

        $user = $this->tokenStorage->getToken()->getUser();

        if(!$user instanceof User){
            return;
        }

        $request->attributes->set('id', $user->getId());

    }
}
