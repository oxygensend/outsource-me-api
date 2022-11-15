<?php

namespace App\Event\Notification;

use App\Entity\Application;
use App\Entity\Message;
use App\Entity\User;

class NotificationText
{

    public static function getJobOfferApplicationInternalText(User $user): string
    {
        return sprintf('Użytkownik %s własnie zaaplikował na twoja oferte. Sprawdź teraz!', $user);
    }

    public static function getJobOfferApplicationEmailText(Application $application): string
    {
        $user = $application->getJobOffer()->getUser();
        return sprintf('Użytkownik %s własnie zaaplikował na twoja oferte. Sprawdź teraz!', $user);
    }

    public static function getMessageEventInternalText(Message $message): string
    {
        return sprintf('Użytkownik %s przesłał Ci wiadomość. Sprawdź swoją skrzynke pocztową.', $message->getAuthor());
    }

    public static function getWelcomeMessageAfterRegistrationEmailText(User $user): string
    {
        return "Witamy cię na <b>Outsource me</b>! Tutaj możesz znaleźć dodatkowe zlecenia lub znaleźć programistów.</br> Dziękujemy że jestes z nami. </br> Ekipa Outsource me";

    }

    public static function getWelcomeMessageAfterRegistrationInternalText(User $user): string
    {
        return "Witamy cię na <b>Outsource me</b>! Tutaj możesz znaleźć dodatkowe zlecenia lub znaleźć programistów.";

    }
}