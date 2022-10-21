<?php

namespace App\Controller\Api;

use App\DTO\AddTechnologyRequestDto;
use App\Entity\ConfirmationToken;
use App\Entity\Technology;
use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class AddTechnologyAction extends AbstractController
{

    public function __construct(readonly private UserService $userService)
    {
    }


    public function __invoke(User $user, AddTechnologyRequestDto $request): Technology
    {
        return $this->userService->addTechnology($user, $request->getIri());

    }

}