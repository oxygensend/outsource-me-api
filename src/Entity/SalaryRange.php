<?php

namespace App\Entity;

use App\Repository\SalaryRangeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[\App\Validator\SalaryRange]
#[ORM\Entity(repositoryClass: SalaryRangeRepository::class)]
class SalaryRange extends AbstractEntity
{

    public const CURRENCIES_CHOICES = ['PL', 'EUR', 'USD'];
    public const TYPE_CHOICES = ['brutto', 'netto'];

    #[Assert\NotBlank]
    #[Serializer\Groups(['jobOffer:write'])]
    #[ORM\Column]
    private ?float $downRange = null;

    #[Serializer\Groups(['jobOffer:write'])]
    #[ORM\Column(nullable: true)]
    private ?float $upRange = null;

    #[Assert\Choice(choices: self::CURRENCIES_CHOICES, message: "The {{ value }} is not a valid choice.Valid choices: {{ choices }}")]
    #[Serializer\Groups(['jobOffer:write'])]
    #[ORM\Column(length: 3)]
    private ?string $currency = null;

    #[Assert\Choice(choices: self::TYPE_CHOICES, message: "The {{ value }} is not a valid choice.Valid choices: {{ choices }}")]
    #[Serializer\Groups(['jobOffer:write', 'jobOffer:one'])]
    #[ORM\Column(length: 8, nullable: true)]
    private ?string $type = null;

    public function getDownRange(): ?float
    {
        return $this->downRange;
    }

    public function setDownRange(float $downRange): self
    {
        $this->downRange = $downRange;

        return $this;
    }

    public function getUpRange(): ?float
    {
        return $this->upRange;
    }

    public function setUpRange(?float $upRange): self
    {
        $this->upRange = $upRange;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    #[Serializer\Groups(['jobOffer:one'])]
    public function getSalaryRange(): string
    {
        if ($this->getUpRange()) {
            $salaryRange = $this->getDownRange() . ' - ' . $this->getUpRange() . ' ' . $this->currency;
        } else {
            $salaryRange = $this->getDownRange() . ' ' . $this->currency;
        }

        return $salaryRange;
    }
}
