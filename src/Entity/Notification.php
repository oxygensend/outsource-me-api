<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use App\Repository\NotificationRepository;
use App\State\Processor\DeleteNotificationProcessor;
use App\State\Processor\MarkNotificationSeenProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/users/{id}/notifications',
            uriVariables: [
                'id' => new Link(toProperty: 'receiver', fromClass: User::class)
            ],
            paginationEnabled: true,
            paginationItemsPerPage: 14,
            security: "is_granted('GET_NOTIFICATIONS',_api_normalization_context['uri_variables'])"

        ),
        new Post(
            uriTemplate: '/users/{id}/notifications/{notificationId}/mark_seen',
            uriVariables: [
                'id' => new Link(toProperty: 'receiver', fromClass: User::class),
                'notificationId' => new Link(fromClass: Notification::class),
            ],
            normalizationContext: ['groups' => ['notification:displayedAt']],
            security: "is_granted('GET_NOTIFICATIONS',_api_normalization_context['uri_variables'])",
            processor: MarkNotificationSeenProcessor::class

        ),
        new Delete(
            processor: DeleteNotificationProcessor::class
        )

    ],
    normalizationContext: ['groups' => ['notifications:get'], 'skip_null_values' => false],
)]
#[ApiFilter(OrderFilter::class, properties: ['createdAt'])]
#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification extends AbstractEntity
{

    public const CHANNEL_EMAIL = 'email';
    public const CHANNEL_INTERNAL = 'internal';


    #[Serializer\Groups(['notifications:get'])]
    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $receiver = null;

    #[ORM\Column(length: 10)]
    private ?string $channel = null;

    #[Serializer\Groups(['notifications:get', 'notification:displayedAt'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $displayedAt = null;

    #[ORM\ManyToOne]
    private ?Application $relatedApplication = null;

    #[ORM\ManyToOne]
    private ?Message $relatedMessage = null;

    #[ORM\Column]
    private ?bool $deleted = false;

    #[Serializer\Groups(['notifications:get'])]
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

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

    public function isDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }
}
