<?php

namespace App\Entity;

use App\Repository\LanguageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Entity(repositoryClass: LanguageRepository::class)]
class Language extends AbstractEntity
{
    #[Serializer\Groups(['user:profile'])]
    #[ORM\Column(length: 3)]
    private ?string $name = null;

    #[Serializer\Groups(['user:profile'])]
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
