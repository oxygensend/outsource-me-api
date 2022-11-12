<?php

namespace App\Security\Voter;

use App\Entity\JobOffer;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class JobOfferVoter extends Voter
{
    public const CREATE_JOB_OFFER = 'CREATE_JOB_OFFER';
    public const DELETE_JOB_OFFER = 'DELETE_JOB_OFFER';
    public const EDIT_JOB_OFFER = 'EDIT_JOB_OFFER';
    public const GET_USER_JOB_OFFERS = 'GET_USER_JOB_OFFERS';

    public const ALLOWED_ROLES = [
        'ROLE_PRINCIPLE',
        'ROLE_ADMIN'
    ];

    public function __construct(readonly private UserRepository $userRepository)
    {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return ((in_array($attribute, [self::EDIT_JOB_OFFER, self::DELETE_JOB_OFFER])
                && $subject instanceof JobOffer)
            || ($attribute === self::CREATE_JOB_OFFER && !$subject)
            || ($attribute === self::GET_USER_JOB_OFFERS && is_array($subject))

        );
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::CREATE_JOB_OFFER:

                if ($this->checkIfRolesAreAllowed($user)) {
                    return true;
                }
                break;
            case self::DELETE_JOB_OFFER:
            case self::EDIT_JOB_OFFER:
                if (
                    $this->checkIfRolesAreAllowed($user)
                    && $user === $subject->getUser()
                ) {
                    return true;
                }

                break;
            case self::GET_USER_JOB_OFFERS:
                $resource = $this->userRepository->find($subject['userId']);
                if ($resource === $user && in_array('ROLE_PRINCIPLE', $user->getRoles())) {
                    return true;
                }
                break;
        }

        return false;
    }


    private function checkIfRolesAreAllowed(UserInterface $user): bool
    {

        return !empty(array_intersect($user->getRoles(), self::ALLOWED_ROLES));

    }
}
