<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Opinion;
use App\Repository\OpinionRepository;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Security;

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
        $receiver = $data->getToWho();


        // TODO move to event
        $opinions = $this->opinionRepository->findBy(['toWho' => $receiver]);
        $rate = $data->getScale();
        foreach ($opinions as $op){
           $rate += $op->getScale();
        }
        $receiver->setOpinionsRate($rate/(count($opinions) +1));


        $this->decoratedProcessor->process($data, $operation, $uriVariables, $context);
    }
}
