<?php

namespace App\Event\Notification;

use App\Entity\Application;
use App\Entity\User;

class JobOfferApplicationEvent extends AbstractNotificationEvent
{

    public function __construct(Application $application)
    {
        $this->setRelatedApplication($application);
    }

}