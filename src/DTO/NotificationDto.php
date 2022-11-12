<?php

namespace App\DTO;

use App\Entity\Application;
use App\Entity\JobOffer;
use App\Entity\Message;
use App\Entity\User;

class NotificationDto
{
    private ?User $relatedUser = null;
    private ?JobOffer $relatedJobOffer = null;
    private ?Application $relatedApplication = null;
    private ?Message $relatedMessage = null;
    private ?string $channel = null;
    private ?User $receiver = null;
    private ?string $content = null;


    public function getRelatedUser(): ?User
    {
        return $this->relatedUser;
    }

    public function setRelatedUser(?User $relatedUser): void
    {
        $this->relatedUser = $relatedUser;
    }

    public function getRelatedJobOffer(): ?JobOffer
    {
        return $this->relatedJobOffer;
    }

    public function setRelatedJobOffer(?JobOffer $relatedJobOffer): void
    {
        $this->relatedJobOffer = $relatedJobOffer;
    }

    public function getRelatedApplication(): ?Application
    {
        return $this->relatedApplication;
    }

    public function setRelatedApplication(?Application $relatedApplication): void
    {
        $this->relatedApplication = $relatedApplication;
    }

    public function getChannel(): ?string
    {
        return $this->channel;
    }

    public function setChannel(?string $channel): void
    {
        $this->channel = $channel;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): void
    {
        $this->receiver = $receiver;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    public function getRelatedMessage(): ?Message
    {
        return $this->relatedMessage;
    }

    public function setRelatedMessage(?Message $relatedMessage): void
    {
        $this->relatedMessage = $relatedMessage;
    }


}