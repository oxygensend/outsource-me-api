<?php

namespace App\EventSubscriber;

use App\Entity\AboutUs;
use App\Entity\User;
use App\Service\ImageService;
use Doctrine\ORM\EntityManagerInterface;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events;
use Vich\UploaderBundle\Exception\NotUploadableException;

class VichUploaderSubscriber implements EventSubscriberInterface
{

    public function __construct(readonly private FilterManager          $filterManager,
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

        $optimizer = new ImageService($this->filterManager, $event);
        $optimizer->optimize();

    }

    /** Transform user thumbnail to smaller format */
    public function onVichUploaderPostUpload(Event $event): void
    {
        if (!$event->getObject() instanceof User) {
            return;
        }
        /** @var User $user */
        $user = $event->getObject();

        $optimizer = new ImageService($this->filterManager, $event);

        $imageFile = $user->getImageFile();
        $newFileName = str_replace('.webp', '-thumbnail.webp', $imageFile->getFilename());
        $processedImage = $optimizer->applyFilter($imageFile, 'user_thumbnail', $newFileName);
        $path = $imageFile->getPath() . '/' . $newFileName;

        if (!file_put_contents($path, $processedImage)) {
            throw new NotUploadableException('Failed creating thumbnail for user ' . $user->getSlug());
        }

        $user->setImageNameSmall($newFileName);
        $this->em->flush();
    }


}