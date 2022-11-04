<?php

namespace App\Service;

use App\DTO\NotificationDto;
use App\Entity\Notification;
use App\Entity\User;
use App\Event\Notification\AbstractNotificationEvent;
use App\Event\Notification\JobOfferApplicationEvent;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    public function __construct(readonly private EmailSenderService     $emailSender,
                                readonly private EntityManagerInterface $em
    )
    {
    }

    public function sendNotifications(AbstractNotificationEvent $event, array $notificationsDto): void
    {
        /** @var NotificationDto $dto */
        foreach ($notificationsDto as $dto) {
            $notification = new Notification();
            $notification->setChannel($dto->getChannel());
            $notification->setRelatedApplication($dto->getRelatedApplication());
            $notification->setReceiver($dto->getReceiver());
            $notification->setContent($dto->getContent());

            if ($notification->getChannel() === Notification::CHANNEL_EMAIL) {

                $notification->setDisplayedAt(new \DateTime());
                $this->sendMail($event, $notification);
            }

            $this->em->persist($notification);
            $this->em->flush($notification);


        }
    }

    private function sendMail(AbstractNotificationEvent $event, Notification $notification): void
    {

        if (get_class($event) === JobOfferApplicationEvent::class) {
            $this->emailSender->sendJobOfferApplicationEmail(
                $notification->getReceiver(),
                $notification->getRelatedApplication(),
                $notification->getContent()
            );
        }

    }


}