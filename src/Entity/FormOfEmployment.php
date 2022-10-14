<?php

namespace App\Entity;

use App\Repository\FormOfEmploymentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;


#[ORM\Entity(repositoryClass: FormOfEmploymentRepository::class)]
class FormOfEmployment extends AbstractEntity
{
    #[Serializer\Groups(['user:profile'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
