<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\EducationRepository;
use App\State\EducationProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            denormalizationContext: ['groups' => 'education:write'],
            security: "is_granted('ROLE_USER')",
            processor: EducationProcessor::class
        ),
        new Patch(
            denormalizationContext: ['groups' => 'education:edit'],
            security: "is_granted('ROLE_USER') and is_granted('USER_EDIT', object.getIndividual())"
        ),
        new GetCollection(
            uriTemplate: '/users/{userId}/educations',
            uriVariables: [
                'userId' => new Link(toProperty: 'individual', fromClass: User::class)
            ]
        ),
        new Delete(
            uriTemplate: '/users/{userId}/educations/{id}',
            uriVariables: [
                'userId' => new Link(toProperty: 'individual', fromClass: User::class),
                'id' => new Link(fromClass: Education::class),
            ],
            security: "is_granted('ROLE_USER') and is_granted('USER_EDIT', object.getIndividual())"
        )
    ],
    normalizationContext: ['groups' => 'education:read'],
)]
#[ORM\Entity(repositoryClass: EducationRepository::class)]
class Education extends AbstractEntity
{
    #[Assert\NotBlank]
    #[Serializer\Groups(["user:profile", "education:read", "education:write", "education:edit"])]
    #[ORM\ManyToOne]
    private ?University $university = null;

    #[Assert\Type("\DateTimeInterface")]
    #[Assert\NotBlank]
    #[Serializer\Groups(["user:profile", "education:read", "education:write", "education:edit"])]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;


    #[Assert\Type("\DateTimeInterface")]
    #[Serializer\Groups(["user:profile", "education:read", "education:write", "education:edit"])]
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endDate = null;

    #[Assert\NotBlank]
    #[Serializer\Groups(["user:profile", "education:read", "education:write", "education:edit"])]
    #[ORM\Column(length: 255)]
    private ?string $fieldOfStudy = null;

    #[Serializer\Groups(["user:profile", "education:read", "education:write", "education:edit"])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[Serializer\Groups(["user:profile", "education:read", "education:write", "education:edit"])]
    #[ORM\Column(nullable: true)]
    private ?float $grade = null;

    #[Serializer\Groups(["user:profile", "education:read", "education:write", "education:edit"])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'educations')]
    private ?User $individual = null;


    public function getUniversity(): ?University
    {
        return $this->university;
    }

    public function setUniversity(?University $university): self
    {
        $this->university = $university;

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

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getFieldOfStudy(): ?string
    {
        return $this->fieldOfStudy;
    }

    public function setFieldOfStudy(?string $fieldOfStudy): self
    {
        $this->fieldOfStudy = $fieldOfStudy;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getGrade(): ?float
    {
        return $this->grade;
    }

    public function setGrade(?float $grade): self
    {
        $this->grade = $grade;

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
}
