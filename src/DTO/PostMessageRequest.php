<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class PostMessageRequest extends AbstractRequestDto
{

    #[Assert\NotBlank]
    protected string $content;

    #[Assert\NotBlank]
    protected string $subject;

    #[Assert\NotBlank]
    protected string $receiverIri;

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function getReceiverIri(): string
    {
        return $this->receiverIri;
    }

    public function setReceiverIri(string $receiver): void
    {
        $this->receiverIri = $receiver;
    }


}