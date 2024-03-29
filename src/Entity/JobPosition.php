<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\JobPositionRepository;
use App\State\Processor\JobPositionProcessor;
use App\Validator\DateTimeRange;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            denormalizationContext: ['groups' => 'job_position:write'],
            security: "is_granted('ROLE_USER')",
            processor: JobPositionProcessor::class
        ),
        new Patch(
            denormalizationContext: ['groups' => 'job_position:edit'],
            security: "is_granted('ROLE_USER') and is_granted('USER_EDIT', object.getIndividual())",
            processor: JobPositionProcessor::class
        ),
        new GetCollection(
            uriTemplate: '/users/{userId}/job_positions',
            uriVariables: [
                'userId' => new Link(toProperty: 'individual', fromClass: User::class)
            ]
        ),
        new Delete(
            uriTemplate: '/users/{userId}/job_positions/{id}',
            uriVariables: [
                'userId' => new Link(toProperty: 'individual', fromClass: User::class),
                'id' => new Link(fromClass: JobPosition::class),
            ],
            security: "is_granted('ROLE_USER') and is_granted('USER_EDIT', object.getIndividual())"
        )
    ],
    normalizationContext: ['groups' => 'job_position:read'],
)]
#[DateTimeRange]
#[ORM\Entity(repositoryClass: JobPositionRepository::class)]
class JobPosition extends AbstractEntity
{
    #[Assert\NotBlank]
    #[Serializer\Groups(['user:profile', 'job_position:read', 'job_position:write', 'job_position:edit', 'user:get', 'jobOffer:get'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Assert\NotBlank]
    #[Serializer\Groups(['user:profile', 'job_position:read', 'job_position:write', 'job_position:edit'])]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?FormOfEmployment $formOfEmployment = null;

    #[Assert\NotBlank]
    #[Serializer\Groups(['user:profile', 'job_position:read', 'job_position:write', 'job_position:edit'])]
    #[ORM\ManyToOne()]
    private ?Company $company = null;

    #[Serializer\Groups(['user:profile', 'job_position:read', 'job_position:write', 'job_position:edit'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'jobPositions')]
    private ?User $individual = null;

    #[Assert\Type("\DateTimeInterface")]
    #[Serializer\Groups(['user:profile', 'job_position:read', 'job_position:write', 'job_position:edit'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startDate = null;

    #[Assert\Type("\DateTimeInterface")]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Serializer\Groups(['user:profile', 'job_position:read', 'job_position:write', 'job_position:edit'])]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column]
    private ?bool $active = false;


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFormOfEmployment(): ?FormOfEmployment
    {
        return $this->formOfEmployment;
    }

    public function setFormOfEmployment(?FormOfEmployment $formOfEmployment): self
    {
        $this->formOfEmployment = $formOfEmployment;

        return $this;
    }


    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

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

    public function getIndividual(): ?User
    {
        return $this->individual;
    }

    public function setIndividual(?User $individual): self
    {
        $this->individual = $individual;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}
