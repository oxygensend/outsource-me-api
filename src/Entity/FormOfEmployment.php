<?php

namespace App\Entity;

use App\Repository\FormOfEmploymentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormOfEmploymentRepository::class)]
class FormOfEmployment extends AbstractEntity
{
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
