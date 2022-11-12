<?php

namespace App\Event\Notification;

use App\Entity\Message;

class MessageEvent extends AbstractNotificationEvent
{

    public function __construct(Message $message)
    {
        $this->setRelatedMessage($message);
    }
}