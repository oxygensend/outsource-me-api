<?php

namespace App\Security\Voter;

use App\Entity\Attachment;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AttachmentVoter extends Voter
{
    public const DOWNLOAD_ATTACHMENT = 'DOWNLOAD_ATTACHMENT';

    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === self::DOWNLOAD_ATTACHMENT && $subject instanceof Attachment;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Attachment $subject */
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::DOWNLOAD_ATTACHMENT:

                $jobOfferCreator = $subject->getApplication()->getJobOffer()->getUser();

                if($user === $subject->getCreatedBy() || $jobOfferCreator === $user){
                    return true;
                }

                break;
        }

        return false;
    }
}
