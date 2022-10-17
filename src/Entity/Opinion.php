<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\OpinionRepository;
use App\State\OpinionProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints\Range;

#[ApiResource(
    operations: [
        new Post(
            denormalizationContext: ['groups' => 'opinions:write'],
            security: "is_granted('ROLE_USER')",
            processor: OpinionProcessor::class
        ),
        new GetCollection(
            uriTemplate: '/users/{userId}/opinions',
            uriVariables: [
                'userId' => new Link(toProperty: 'toWho', fromClass: User::class)
            ],
            paginationItemsPerPage: 5,
        ),
        new Patch(
            denormalizationContext: ['groups' => 'opinions:edit'],
            securityPostDenormalize: "is_granted('ROLE_USER') and is_granted('EDIT_OPINION', object.getFromWho())",
        ),
        new Delete(
            uriTemplate: '/users/{userId}/opinions/{id}',
            uriVariables: [
                'userId' => new Link(toProperty: 'fromWho', fromClass: User::class),
                'id' => new Link(fromClass: Opinion::class),
            ],
            security: "is_granted('ROLE_USER') and is_granted('EDIT_OPINION', object.getFromWho())"
        )
    ],
    normalizationContext: ["groups" => "opinions:get"]

)]
#[ORM\Entity(repositoryClass: OpinionRepository::class)]
class Opinion extends AbstractEntity
{
    #[Serializer\Groups(['opinions:get', 'opinions:edit', 'opinions:write'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[Range(notInRangeMessage: "Scale must be in range 0 to 5", min: 0, max: 5)]
    #[Serializer\Groups(['opinions:get', 'opinions:edit', 'opinions:write'])]
    #[ORM\Column]
    private ?int $scale = null;

    #[Serializer\Groups(['opinions:get'])]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $fromWho = null;

    #[Serializer\Groups(['opinions:write'])]
    #[ORM\ManyToOne(inversedBy: 'opinions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $toWho = null;


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getScale(): ?int
    {
        return $this->scale;
    }

    public function setScale(int $scale): self
    {
        $this->scale = $scale;

        return $this;
    }

    public function getFromWho(): ?User
    {
        return $this->fromWho;
    }

    public function setFromWho(?User $fromWho): self
    {
        $this->fromWho = $fromWho;

        return $this;
    }

    public function getToWho(): ?User
    {
        return $this->toWho;
    }

    public function setToWho(?User $toWho): self
    {
        $this->toWho = $toWho;

        return $this;
    }
}
