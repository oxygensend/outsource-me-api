<?php

namespace App\EventSubscriber\Notification;

use App\Entity\Notification;
use App\Event\Notification\AbstractNotificationEvent;
use App\Event\Notification\MessageEvent;
use App\Event\Notification\NotificationText;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MessageSubscriber extends AbstractNotificationSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedNotificationEvents(): array
    {
        return [MessageEvent::class];

    }

    protected function getChannels(): array
    {
        return [
            Notification::CHANNEL_INTERNAL
        ];
    }

    protected function getReceivers(AbstractNotificationEvent $event): array
    {
        return [$event->getRelatedMessage()->getReceiver()];
    }

    protected function getInternalNotificationText(AbstractNotificationEvent $event): ?string
    {
        return NotificationText::getMessageEventInternalText($event->getRelatedMessage());
    }


    protected function getEmailNotificationText(AbstractNotificationEvent $event): ?string
    {
        return null;
    }
}