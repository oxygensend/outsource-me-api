<?php

namespace App\EventSubscriber;

use App\Entity\AboutUs;
use App\Entity\User;
use App\Service\ImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events;

class VichUploaderSubscriber implements EventSubscriberInterface
{
    public function __construct(readonly private ImageService           $imageService,
                                readonly private EntityManagerInterface $em)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::PRE_UPLOAD => 'onVichUploaderPreUpload',
            Events::POST_UPLOAD => 'onVichUploaderPostUpload',
        ];
    }

    /** Transform uploaded images to webp format */
    public function onVichUploaderPreUpload(Event $event)
    {
        if (!$event->getObject() instanceof User && !$event->getObject() instanceof AboutUs) {
            return;
        }

        /** @var User|AboutUs $user */
        $object = $event->getObject();
        $imageFile = $object->getImageFile();

        $this->imageService->formatImageToWebP(
            $imageFile->getContent(),
            $imageFile->getPathname(),
            $imageFile->getMimeType()
        );

    }

    /** Transform user thumbnail to smaller format */
    public function onVichUploaderPostUpload(Event $event): void
    {
        if (!$event->getObject() instanceof User) {
            return;
        }
        /** @var User $user */
        $user = $event->getObject();

        $imageFile = $user->getImageFile();
        $newFileName = str_replace('.webp', '-thumbnail.webp', $imageFile->getFilename());
        $this->imageService->applyFilter($imageFile, 'user_thumbnail', $newFileName);

        $user->setImageNameSmall($newFileName);
        $this->em->flush();
    }


}