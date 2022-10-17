<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Opinion;
use App\Repository\OpinionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class OpinionProcessor implements ProcessorInterface
{
    public function __construct(private readonly ProcessorInterface $decoratedProcessor,
                                private readonly OpinionRepository  $opinionRepository,
                                private readonly Security           $security)
    {
    }

    /**
     * @param Opinion $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {

        $user = $this->security->getUser();
        if ($user === $data->getToWho()) {
            throw new AccessDeniedHttpException('Opinion self-assignment is forbidden');
        }

        $opinion = $this->opinionRepository->findOpinionsRelatedToUsers($user, $data->getToWho());
        if ($opinion) {
            throw new AccessDeniedHttpException('You can only assign one opinion to one user');
        }

        $data->setFromWho($user);
        $this->decoratedProcessor->process($data, $operation, $uriVariables, $context);
    }
}
