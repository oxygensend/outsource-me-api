<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification extends AbstractEntity
{

    public const CHANNEL_EMAIL = 'email';
    public const CHANNEL_INTERNAL = 'internal';


    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $receiver = null;

    #[ORM\Column(length: 10)]
    private ?string $channel = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $displayedAt = null;

    #[ORM\ManyToOne]
    private ?Application $relatedApplication = null;

    #[ORM\ManyToOne]
    private ?Message $relatedMessage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getChannel(): ?string
    {
        return $this->channel;
    }

    public function setChannel(string $channel): self
    {
        $this->channel = $channel;

        return $this;
    }


    public function getDisplayedAt(): ?\DateTimeInterface
    {
        return $this->displayedAt;
    }

    public function setDisplayedAt(\DateTimeInterface $displayedAt): self
    {
        $this->displayedAt = $displayedAt;

        return $this;
    }

    public function getRelatedApplication(): ?Application
    {
        return $this->relatedApplication;
    }

    public function setRelatedApplication(?Application $relatedApplication): self
    {
        $this->relatedApplication = $relatedApplication;

        return $this;
    }

    public function getRelatedMessage(): ?Message
    {
        return $this->relatedMessage;
    }

    public function setRelatedMessage(?Message $relatedMessage): self
    {
        $this->relatedMessage = $relatedMessage;

        return $this;
    }
}
