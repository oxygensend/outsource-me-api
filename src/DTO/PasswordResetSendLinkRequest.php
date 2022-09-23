<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class PasswordResetSendLinkRequest extends AbstractRequestDto
{

    #[Assert\Email]
    #[Assert\NotBlank(message: 'Property email cannot be empty.')]
    public ?string $email = null;

}