<?php

namespace App\DTO;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Constraints as Assert;

class PostApplicationRequest extends AbstractRequestDto
{
    #[Assert\NotBlank]
    protected string $jobOfferIri;

    protected string $description;


    #[Assert\Type(type: 'array')]
    #[Assert\All([
        new Assert\File(
            maxSize: '2M',
            mimeTypes: ['application/pdf', 'application/x-pdf', 'image/jpeg', 'image/png', 'image/webp']
        )
    ])]
    /** @var File[] */
    protected ?array $attachments;

    public function __construct(Request $request)
    {
        $this->attachments = $request->files->get('attachments');
        $this->description = $request->get('description');
        $this->jobOfferIri = $request->get('jobOffer');


        if (!isset($this->jobOfferIri)) {
            throw new BadRequestHttpException("Property 'jobOfferIri' is required.");
        }

    }

    public function getJobOfferIri(): string
    {
        return $this->jobOfferIri;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAttachments(): ?array
    {
        return $this->attachments;
    }


}