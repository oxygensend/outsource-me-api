<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Repository\ConfirmationTokenRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: "/confirmation_token/{type}/{token}",
            routeName: 'user_confirmation_token',
            openapiContext: [
                'summary' => 'Verifies confirmation token for user.',
                'description' => 'Verifies confirmation token sent to user by external message. Used for email verification and password reset confirmation',
                'parameters' => [
                    [
                        'name' => 'type',
                        'in' => 'path',
                        'description' => 'Type of token',
                        'required' => true
                    ],
                    [
                        'name' => 'token',
                        'in' => 'path',
                        'description' => 'Confirmation token',
                        'required' => true
                    ]
                ],
                'responses' => [
                    '302' => [
                        'description' => 'Token accepted. Redirecting to configured URL.'
                    ],
                    '200' => null,
                    '400' => null,
                    '422' => null
                ]
            ]
        )
    ]
)]
#[ORM\Entity(repositoryClass: ConfirmationTokenRepository::class)]
class ConfirmationToken extends AbstractEntity
{

    public const REGISTRATION_TYPE = 'registration';
    public const RESET_PASSWORD_TYPE = 'reset_password';
    public const RESET_PASSWORD_EXECUTE_TYPE = 'reset_password_execute';


    #[ORM\Column(length: 30)]
    private ?string $type = null;

    #[ORM\Column(length: 64)]
    private ?string $token = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $expiredAt = null;


    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getExpiredAt(): ?\DateTimeInterface
    {
        return $this->expiredAt;
    }

    public function setExpiredAt(\DateTimeInterface $expiredAt): self
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }
}
