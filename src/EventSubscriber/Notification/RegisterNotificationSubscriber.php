<?php

namespace App\EventSubscriber\Notification;

use App\Entity\Notification;
use App\Event\Notification\AbstractNotificationEvent;
use App\Event\Notification\MessageEvent;
use App\Event\Notification\NotificationText;
use App\Event\Notification\RegisterEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RegisterNotificationSubscriber extends AbstractNotificationSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedNotificationEvents(): array
    {
        return [RegisterEvent::class];

    }

    protected function getChannels(): array
    {
        return [
            Notification::CHANNEL_INTERNAL,
            Notification::CHANNEL_EMAIL
        ];
    }

    protected function getReceivers(AbstractNotificationEvent $event): array
    {
        return [$event->getRelatedUser()];
    }

    protected function getInternalNotificationText(AbstractNotificationEvent $event): ?string
    {
        return NotificationText::getWelcomeMessageAfterRegistrationInternalText($event->getRelatedUser());
    }


    protected function getEmailNotificationText(AbstractNotificationEvent $event): ?string
    {
        return NotificationText::getWelcomeMessageAfterRegistrationEmailText($event->getRelatedUser());
    }

}