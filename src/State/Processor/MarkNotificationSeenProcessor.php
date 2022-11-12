<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Notification;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class MarkNotificationSeenProcessor implements ProcessorInterface
{
    public function __construct(readonly private ProcessorInterface $decoratedProcessor)
    {
    }

    /**
     * @param Notification $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($data->getDisplayedAt() !== null) {
            throw new UnauthorizedHttpException('Unauthorized', 'This notification was already mark as seen.');
        }
        $data->setDisplayedAt(new \DateTime());

        $this->decoratedProcessor->process($data, $operation, $uriVariables, $context);

    }
}
