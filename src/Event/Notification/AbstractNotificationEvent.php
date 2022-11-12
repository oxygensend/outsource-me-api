<?php

namespace App\Event\Notification;

use App\Entity\Application;
use App\Entity\JobOffer;
use App\Entity\Message;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractNotificationEvent extends Event
{
    private ?Application $relatedApplication = null;
    private ?User $relatedUser = null;
    private ?JobOffer $relatedJobOffer = null;
    private ?string $relatedContent = null;
    private ?string $relatedChannel = null;
    private ?Message $relatedMessage = null;


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

    public function getRelatedUser(): ?User
    {
        return $this->relatedUser;
    }

    public function setRelatedUser(?User $relatedUser): void
    {
        $this->relatedUser = $relatedUser;
    }

    public function getRelatedContent(): ?string
    {
        return $this->relatedContent;
    }

    public function setRelatedContent(?string $relatedContent): void
    {
        $this->relatedContent = $relatedContent;
    }

    public function getRelatedChannel(): ?string
    {
        return $this->relatedChannel;
    }

    public function setRelatedChannel(?string $relatedChannel): void
    {
        $this->relatedChannel = $relatedChannel;
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