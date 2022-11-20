<?php

namespace App\Controller\Api;

use App\DTO\UploadUserPhotoRequest;
use App\Entity\User;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class UploadUserPhotoAction
{

    public function __construct(readonly private UserService $userService)
    {
    }

    public function __invoke(User $user, UploadUserPhotoRequest $request): Response
    {
        $this->userService->uploadPhoto($user, $request->getFile());

        return new JsonResponse('Photo uploaded');
    }
}