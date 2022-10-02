<?php

namespace App\Entity;

use App\Repository\JobPositionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Entity(repositoryClass: JobPositionRepository::class)]
class JobPosition extends AbstractEntity
{
    #[Serializer\Groups(['user:profile'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Serializer\Groups(['user:profile'])]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?FormOfEmployment $formOfEmployment = null;

    #[Serializer\Groups(['user:profile'])]
    #[ORM\Column(length: 255)]
    private ?string $companyName = null;

    #[Serializer\Groups(['user:profile'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'jobPositions')]
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

    public function getFormOfEmployment(): ?FormOfEmployment
    {
        return $this->formOfEmployment;
    }

    public function setFormOfEmployment(?FormOfEmployment $formOfEmployment): self
    {
        $this->formOfEmployment = $formOfEmployment;

        return $this;
    }


    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): self
    {
        $this->companyName = $companyName;

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
