<?php

namespace App\Service;

use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Liip\ImagineBundle\Model\Binary;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Exception\NotUploadableException;

class ImageService
{

    public function __construct(readonly private FilterManager $filterManager)
    {
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

    public function applyFilter(File $imageFile, string $filter, ?string $newFileName = null): void
    {
        $binaryImage = new Binary($imageFile->getContent(), null, null);

        $processedImage = $this->filterManager->applyFilter($binaryImage, $filter);
        $path = $newFileName ? $imageFile->getPath() . '/' . $newFileName : $imageFile->getPathname();

        if (!file_put_contents($path, $processedImage->getContent())) {
            throw new NotUploadableException('Failed formatting image to thumbnail version.');
        }
    }

}