<?php

namespace App\DTO;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class UploadUserPhotoRequest extends AbstractRequestDto
{

    #[Assert\NotBlank]
    #[Assert\File(maxSize: '2M', mimeTypes: ['image/webp', 'image/jpeg', 'image/jpg', 'image/png'])]
    protected File $file;



    public function __construct(Request $request)
    {
        $this->file = $request->files->get('file');
    }

    public function getFile(): File
    {
        return $this->file;
    }

    public function setFile(File $file): void
    {
        $this->file = $file;
    }



}