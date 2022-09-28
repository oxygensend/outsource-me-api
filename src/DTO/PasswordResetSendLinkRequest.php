<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class PasswordResetSendLinkRequest extends AbstractRequestDto
{

    #[Assert\Email]
    #[Assert\NotBlank(message: 'Property email cannot be empty.')]
    protected ?string $email = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

}