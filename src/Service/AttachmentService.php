<?php

namespace App\Service;

use App\Entity\Attachment;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Security;

class AttachmentService
{
    public function __construct(readonly private Security $security)
    {
    }

    public function saveAttachment(UploadedFile $file): Attachment
    {
        $originalName = $file->getClientOriginalName();
        $size = $file->getSize();
        $attachment = new Attachment();
        $attachment->setFile($file);
        $attachment->setOriginalName($originalName);
        $attachment->setSize($size);
        $attachment->setCreatedBy($this->security->getUser());


        return $attachment;

    }
}