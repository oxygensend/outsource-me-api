<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use App\Controller\Api\PostApplicationController;
use App\Repository\ApplicationRepository;
use App\State\Processor\DeleteApplicationProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;


#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/applications',
            controller: PostApplicationController::class,
            openapiContext: [
                'summary' => 'Aplicate for Job Offer',
                'description' => 'Create application for Job Offer',
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'attachments' => [
                                        'type' => 'array',
                                        'format' => 'binary[]'
                                    ],
                                    'description' => [
                                        'type' => 'string'
                                    ],
                                    'jobOfferIri' => [
                                        'type' => 'string'
                                    ],

                                ]
                            ]
                        ]
                    ]
                ],
                'responses' => [
                    '200' => null,
                    '201' => [
                        'description' => 'Application was created successfully.'
                    ],
                    '400' => null,
                    '401' => null,
                    '422' => null
                ]
            ],
            security: "is_granted('APPLICATION_FOR_JOB_OFFER', user)",
            deserialize: false
        ),
        new Get(
            cacheHeaders: [
                'max_age' => 86400,
                'shared_max_age' => 86400,
            ],
            normalizationContext: ['groups' => ['application:one']],
            security: "is_granted('APPLICATION_VIEW', object)"
        ),
        new Delete(
            security: "is_granted('APPLICATION_DELETE', object)",
            processor: DeleteApplicationProcessor::class
        ),
        new Post(
            uriTemplate: '/applications/{id}/change_status',
            normalizationContext: ['groups' => 'application:status-change'],
            denormalizationContext: ['groups' => 'application:status-change'],
            security: "is_granted('APPLICATION_CHANGE_STATUS', object)",
        ),
        new GetCollection(
            uriTemplate: '/users/{userId}/applications',
            uriVariables: [
                'userId' => new Link(toProperty: 'individual', fromClass: User::class)
            ],
            paginationEnabled: false,
            normalizationContext: ['groups' => ['application:users']],
            security: "is_granted('USER_APPLICATIONS',  _api_normalization_context['uri_variables'])"
        ),

    ],
    order: ['createdAt' => 'ASC']
)]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'status'])]
#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
class Application extends AbstractEntity
{


    #[Serializer\SerializedName('applying_person')]
    #[Serializer\Groups(['application:one', 'jobOffer:management'])]
    #[ORM\ManyToOne(inversedBy: 'applications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $individual = null;

    #[Serializer\Groups(['application:one', 'application:status-change', 'application:users', 'jobOffer:management'])]
    #[ORM\Column]
    private ?int $status = 1;

    #[Serializer\Groups(['application:one','application:users'])]
    #[ORM\ManyToOne(inversedBy: 'applications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?JobOffer $jobOffer = null;

    #[Serializer\Groups(['application:one'])]
    #[ORM\OneToMany(mappedBy: 'application', targetEntity: Attachment::class, cascade: ['persist', 'remove'])]
    private Collection $attachments;

    #[Serializer\Groups(['application:one'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $deleted = false;

    public function __construct()
    {
        parent::__construct();
        $this->attachments = new ArrayCollection();
    }

   #[Serializer\Groups(['application:users', 'jobOffer:management', 'application:one'])]
    public function getCreatedAt(): \DateTimeInterface
    {
       return $this->createdAt;
    }

    public function getIndividual(): ?User
    {
        return $this->individual;
    }

    public function setIndividual(?User $individual): self
    {
        $this->individual = $individual;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getJobOffer(): ?JobOffer
    {
        return $this->jobOffer;
    }

    public function setJobOffer(?JobOffer $jobOffer): self
    {
        $this->jobOffer = $jobOffer;

        return $this;
    }

    /**
     * @return Collection<int, Attachment>
     */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    public function addAttachment(Attachment $attachment): self
    {
        if (!$this->attachments->contains($attachment)) {
            $this->attachments->add($attachment);
            $attachment->setApplication($this);
        }

        return $this;
    }

    public function removeAttachment(Attachment $attachment): self
    {
        if ($this->attachments->removeElement($attachment)) {
            // set the owning side to null (unless already changed)
            if ($attachment->getApplication() === $this) {
                $attachment->setApplication(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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
