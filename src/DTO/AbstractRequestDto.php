<?php

namespace App\DTO;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

abstract class AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(RequestStack $request)
    {
        $body = json_decode($request->getCurrentRequest()->getContent(), true);

        foreach ($body as $name => $value) {
            if (property_exists($this, $name)) {
                $this->{$name} = $value;
            } else {
                throw new BadRequestHttpException('Unknown parameter ' . $name);
            }
        }

    }

}