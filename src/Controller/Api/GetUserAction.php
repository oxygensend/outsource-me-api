<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetUserAction extends AbstractController
{

    public function __construct(readonly private  EntityManagerInterface $em)
    {
    }

    public function __invoke(User $user): User
    {
        $user->addRedirect();
        $this->em->flush();

        return $user;
    }
}