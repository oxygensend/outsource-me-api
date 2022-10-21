<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\LanguageRepository;
use App\State\JobPositionProcessor;
use App\State\LanguageProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    operations: [
        new Post(
            denormalizationContext: ['groups' => 'language:write'],
            security: "is_granted('ROLE_USER')",
            processor: LanguageProcessor::class
        ),
        new Patch(
            denormalizationContext: ['groups' => 'language:edit'],
            security: "is_granted('ROLE_USER') and is_granted('USER_EDIT', object.getIndividual())",
        ),
        new GetCollection(
            uriTemplate: '/users/{userId}/languages',
            uriVariables: [
                'userId' => new Link(toProperty: 'individual', fromClass: User::class)
            ]
        ),
        new Delete(
            uriTemplate: '/users/{userId}/languages/{id}',
            uriVariables: [
                'userId' => new Link(toProperty: 'individual', fromClass: User::class),
                'id' => new Link(fromClass: Language::class),
            ],
            security: "is_granted('ROLE_USER') and is_granted('USER_EDIT', object.getIndividual())"
        )
    ],
    normalizationContext: ['groups' => 'language:read'],
)]
#[ORM\Entity(repositoryClass: LanguageRepository::class)]
class Language extends AbstractEntity
{
    #[NotBlank]
    #[Serializer\Groups(['user:profile', 'language:read', 'language:write', 'language:edit'])]
    #[ORM\Column(length: 30)]
    private ?string $name = null;

    #[Serializer\Groups(['user:profile', 'language:read', 'language:write', 'language:edit'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'languages')]
    private ?User $individual = null;


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
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
