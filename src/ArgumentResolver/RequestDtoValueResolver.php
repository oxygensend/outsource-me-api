<?php

namespace App\ArgumentResolver;

use App\DTO\RequestDtoInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestDtoValueResolver implements ArgumentValueResolverInterface
{

    public function __construct(private readonly ValidatorInterface $validator,
                                private readonly LoggerInterface $logger)
    {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        try {
            $reflectionClass = new \ReflectionClass($argument->getType());
        } catch (\ReflectionException) {
            $this->logger->warning('RequestDtoValueResolver::supports - cannot create class for argument ' . $argument->getType());
            return false;
        }

        if ($reflectionClass->implementsInterface(RequestDtoInterface::class)) {
            return true;
        }

        return false;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $className = $argument->getType();
        $dtoRequest = new $className($request);

        $violations = $this->validator->validate($dtoRequest);

        if ($violations->count() > 0) {
            $errors = $this->extractErrorsFromViolationList($violations);

            throw new BadRequestHttpException($errors);
        }

        yield $dtoRequest;

    }

    private function extractErrorsFromViolationList(ConstraintViolationList $violationList): string
    {
        $response = [];
        foreach ($violationList as $violation) {
            $response[] = $violation->getMessage();
        }

        return join(',', $response);
    }

}