<?php

namespace App\DTO;

use Symfony\Component\HttpFoundation\RequestStack;

interface RequestDtoInterface
{
    public function __construct(RequestStack $request);
}