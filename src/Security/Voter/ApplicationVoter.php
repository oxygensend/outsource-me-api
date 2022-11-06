<?php

namespace App\Security\Voter;

use App\Entity\Application;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use function Symfony\Component\Translation\t;

class ApplicationVoter extends Voter
{
    public const APPLICATION_FOR_JOB_OFFER = 'APPLICATION_FOR_JOB_OFFER';
    public const APPLICATION_VIEW = 'APPLICATION_VIEW';
    public const APPLICATION_DELETE = 'APPLICATION_DELETE';
    public const APPLICATION_CHANGE_STATUS = 'APPLICATION_CHANGE_STATUS';
    public const USER_APPLICATIONS = 'USER_APPLICATIONS';

    public function __construct(readonly private UserRepository $userRepository)
    {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return ($attribute === self::APPLICATION_FOR_JOB_OFFER && $subject instanceof User)
            || (in_array($attribute, [self::APPLICATION_VIEW, self::APPLICATION_DELETE, self::APPLICATION_CHANGE_STATUS])
                && $subject instanceof Application)
            || ($attribute === self::USER_APPLICATIONS && is_array($subject));
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::APPLICATION_FOR_JOB_OFFER:

                if ($user->getAccountType() === User::TYPE_DEVELOPER) {
                    return true;
                }
                break;
            case self::APPLICATION_VIEW:

                /** @var Application $subject */
                if ($subject->getIndividual() === $user || $subject->getJobOffer()->getUser() === $user) {
                    return true;
                }
                break;
            case self::APPLICATION_CHANGE_STATUS:
                if ($subject->getJobOffer()->getUser() === $user) {
                    return true;
                }
                break;
            case self::APPLICATION_DELETE:
                if ($subject->getIndividual() === $user) {
                    return true;
                }
                break;
            case self::USER_APPLICATIONS:
                $resource = $this->userRepository->find($subject['userId']);
                if ($resource === $user && in_array('ROLE_DEVELOPER', $user->getRoles())) {
                    return true;
                }
        }

        return false;
    }
}
