<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\User;
use App\Service\EmailSenderService;
use App\Service\UserService;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SendEmailVerificationSubscriber implements EventSubscriberInterface
{

    public function __construct(private  readonly UserService $userService){}

    #[ArrayShape([KernelEvents::VIEW => "array"])] public static function getSubscribedEvents() : array
    {
        return [
            KernelEvents::VIEW => ['sendMail', EventPriorities::POST_WRITE]
        ];
    }

    /**
     * @throws \Exception
     */
    public function sendMail(ViewEvent $event)
    {
        $data = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$data instanceof User || Request::METHOD_POST !== $method)
            return;


        $this->userService->sendRegistrationConfirmationMessage($data);

    }

}