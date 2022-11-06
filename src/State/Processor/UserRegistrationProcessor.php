<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserRegistrationProcessor implements ProcessorInterface
{
    public function __construct(private readonly ProcessorInterface $decoratedProcessor,
                                private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * @param User $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        switch ($data->getAccountType()) {
            case 'Developer':
                $data->setRoles(['ROLE_DEVELOPER']);
                break;
            case 'Principle':
                $data->setRoles(['ROLE_PRINCIPAL']);
                break;
        }

        $this->setPassword($data);

        $this->decoratedProcessor->process($data, $operation, $uriVariables, $context);
    }

    private function setPassword($data)
    {
        if (($data->getPlainPassword() && $data->getPasswordConfirmation()) &&
            $data->getPlainPassword() === $data->getPasswordConfirmation()) {

            $data->setPassword($this->passwordHasher->hashPassword($data, $data->getPlainPassword()));
            $data->eraseCredentials();
        }
    }

}
