<?php

namespace App\EventSubscriber;

use App\Entity\Notification;
use App\Entity\User;
use App\Event\Notification\AbstractNotificationEvent;
use App\Event\Notification\JobOfferApplicationEvent;
use App\Event\Notification\NotificationText;
use App\EventSubscriber\Notification\AbstractNotificationSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JobOfferApplicationSubscriber extends AbstractNotificationSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedNotificationEvents(): array
    {
        return [JobOfferApplicationEvent::class];

    }

    protected function getChannels(): array
    {
        return [
            Notification::CHANNEL_EMAIL,
            Notification::CHANNEL_INTERNAL
        ];
    }

    protected function getReceivers(AbstractNotificationEvent $event): array
    {
        return [$event->getRelatedApplication()->getJobOffer()->getUser()];
    }

    protected function getInternalNotificationText(AbstractNotificationEvent $event): ?string
    {
        return NotificationText::getJobOfferApplicationInternalText($event->getRelatedApplication()->getJobOffer()->getUser());
    }

    protected function getEmailNotificationText(AbstractNotificationEvent $event): ?string
    {
        return NotificationText::getJobOfferApplicationEmailText($event->getRelatedApplication());

    }
}
