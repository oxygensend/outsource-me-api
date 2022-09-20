<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

#[AsController]
class EmailVerificationController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly VerifyEmailHelperInterface $verifyEmailHelper,
    )
    {}

    public function __invoke(User $data, Request $request ): JsonResponse
    {
        if($data->getEmailConfirmedAt() !== null)
            return $this->json(['error' => 'Your email address is confirmed', 401]);

        try {
            $this->verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $data->getId(),
                $data->getEmail());
            $data->setEmailConfirmedAt(new \DateTime());
            $this->em->persist($data);
            $this->em->flush();

        }  catch(VerifyEmailExceptionInterface $e){

            return $this->json(['error' => $e->getReason(), $e->getCode()]);
        }

        return  $this->json(['success' => 'Your e-mail address has been verified.'], 200);
    }
}