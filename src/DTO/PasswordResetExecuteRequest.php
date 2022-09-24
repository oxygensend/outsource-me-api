<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class PasswordResetExecuteRequest extends AbstractRequestDto
{
    #[Assert\Regex(
        pattern: '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/',
        message: 'Password have to be minimum 8 characters and contains at least one letter and number.'

    )]
    #[Assert\NotBlank]
    protected string $password;

    #[Assert\NotBlank]
    protected string $confirmation_token;


    public function getPassword(): string
    {
        return $this->password;
    }


    public function setPassword(string $password): void
    {
        $this->password = $password;
    }


    public function getConfirmationToken(): string
    {
        return $this->confirmation_token;
    }


    public function setConfirmationToken(string $confirmation_token): void
    {
        $this->confirmation_token = $confirmation_token;
    }


}