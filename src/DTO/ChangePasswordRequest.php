<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordRequest extends AbstractRequestDto
{

    protected string $oldPassword;

    #[Assert\Regex(
        pattern: '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/',
        message: 'Password have to be minimum 8 characters and contains at least one letter and number.'

    )]
    #[Assert\NotBlank]
    protected string $newPassword;

    public function getOldPassword(): string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): void
    {
        $this->newPassword = $newPassword;
    }


}