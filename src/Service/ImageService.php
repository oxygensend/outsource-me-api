<?php

namespace App\Service;

use App\Entity\User;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Liip\ImagineBundle\Model\Binary;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Exception\NotUploadableException;

class ImageService
{
    private object $entity;
    private array $filters = [
        User::class . 'imageFile' => 'user_photo',

    ];

    public function __construct(readonly private FilterManager $filterManager,
                                readonly private Event         $event)
    {
        $this->entity = $event->getObject();
    }

    private function findImageFiles(): array
    {
        $entity = new \ReflectionClass($this->entity);
        $fields = $entity->getProperties();

        $files = [];

        foreach ($fields as $val) {
            $getFunc = property_exists($entity->getName(), $val->name);
            $type = $val->getType()?->getName();
            if ($type === File::class && $getFunc !== false) {

                // If file exists add to que
                if ($this->entity->{"get" . $val->name}() !== null) {
                    $files[] = [
                        'property' => $val->name,
                        'file' => $this->entity->{"get" . $val->name}()
                    ];
                }

            }

        }
        return $files;
    }

    public function getFilter(string $property): ?string
    {

        return $this->filters[$this->entity::class . $property] ?? null;
    }

    public function optimize(): void
    {
        /** @var UploadedFile $file */
        $files = $this->findImageFiles();
        if (!count($files)) return;

        foreach ($files as $file) {

            if (!$file['file'] instanceof UploadedFile) {
                return;
            }

            $imgPath = $file['file']->getPathname();
            $mimeType = $file['file']->getMimeType();
            $filter = $this->getFilter($file['property']);

            if (!$filter) {
                $this->formatImageToWebP($file['file']->getContent(), $imgPath, $mimeType);
                return;
            }

            $binaryImg = $this->applyFilter($file['file'], $filter);
            $this->formatImageToWebP($binaryImg, $imgPath);

        }
    }

    public function formatImageToWebP(string $content, string $path, string $mimeType = 'image/jpeg'): void
    {
        if ($mimeType === 'image/webp') {
            return;
        }

        if (!imagewebp(imagecreatefromstring($content), $path)) {
            throw new NotUploadableException('Failed formatting img to webp.');
        }
    }

    public function applyFilter(File $imageFile, string $filter, ?string $newFileName = null): string
    {
        $binaryImage = new Binary($imageFile->getContent(), null, null);
        return $this->filterManager->applyFilter($binaryImage, $filter)->getContent();

    }

}