<?php

namespace App\Controller\Api;

use App\Entity\Attachment;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Vich\UploaderBundle\Handler\DownloadHandler;

#[AsController]
class DownloadAttachmentAction
{
    public function __construct(readonly private DownloadHandler $downloadHandler)
    {
    }

    public function __invoke(Attachment $attachment): StreamedResponse
    {
        return $this->downloadHandler->downloadObject(
            $attachment,
            'file',
            null,
            $attachment->getName()
        );

    }

}