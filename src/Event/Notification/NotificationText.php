<?php

namespace App\Event\Notification;

use App\Entity\Application;
use App\Entity\User;

class NotificationText
{

    public static function getJobOfferApplicationInternalText(User $user): string
    {
        return sprintf('Użytkownik %s własnie zaaplikował na twoja oferte. Sprawdź teraz!', $user->getFullName());
    }

    public static function getJobOfferApplicationEmailText(Application $application): string
    {
        $user = $application->getJobOffer()->getUser();
        return sprintf('Użytkownik %s własnie zaaplikował na twoja oferte. Sprawdź teraz!', $user->getFullName());
    }
}