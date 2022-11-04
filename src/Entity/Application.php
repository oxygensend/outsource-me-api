<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Controller\Api\PostApplicationController;
use App\Repository\ApplicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


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
            deserialize: false
        )
    ]
)]
#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
class Application extends AbstractEntity
{
    public const APPLICATION_STATUS_OPEN = 1;
    public const APPLICATION_STATUS_CLOSE = 0;


    #[ORM\ManyToOne(inversedBy: 'applications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $individual = null;

    #[ORM\Column]
    private ?int $status = null;

    #[ORM\ManyToOne(inversedBy: 'applications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?JobOffer $jobOffer = null;

    #[ORM\OneToMany(mappedBy: 'application', targetEntity: Attachment::class, cascade: ['persist', 'remove'])]
    private Collection $attachments;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    public function __construct()
    {
        parent::__construct();
        $this->attachments = new ArrayCollection();
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
}
