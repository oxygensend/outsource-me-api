<?php

namespace App\EventSubscriber\Notification;

use App\DTO\NotificationDto;
use App\Entity\Notification;
use App\Entity\User;
use App\Event\Notification\AbstractNotificationEvent;
use App\Service\NotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class AbstractNotificationSubscriber
{

    public function __construct(readonly private NotificationService $notificationService)
    {
    }

    abstract protected function getChannels(): array;

    abstract protected function getReceivers(AbstractNotificationEvent $event): array;

    abstract protected function getInternalNotificationText(AbstractNotificationEvent $event): ?string;

    abstract protected function getEmailNotificationText(AbstractNotificationEvent $event): ?string;

    abstract public static function getSubscribedNotificationEvents(): array;


    public static function getSubscribedEvents(): array
    {
        $events = [];
        foreach (static::getSubscribedNotificationEvents() as $event) {
            $events[$event] = 'actOnEvent';
        }

        return $events;

    }

    public function actOnEvent(AbstractNotificationEvent $event): void
    {

        $notificationsToSend = [];

        /** @var User $receiver */
        foreach ($this->getReceivers($event) as $receiver) {
            foreach ($this->getChannels() as $channel) {

                $notificationDto = new NotificationDto();
                $notificationDto->setChannel($channel);
                $notificationDto->setReceiver($receiver);
                $notificationDto->setContent(
                    $channel === Notification::CHANNEL_EMAIL ?
                        $this->getEmailNotificationText($event) : $this->getInternalNotificationText($event)
                );
                $notificationDto->setRelatedUser($event->getRelatedUser());
                $notificationDto->setRelatedApplication($event->getRelatedApplication());
                $notificationDto->setRelatedJobOffer($event->getRelatedJobOffer());

                $notificationsToSend[] = $notificationDto;
            }

        }

        $this->notificationService->sendNotifications($event, $notificationsToSend);
    }

}