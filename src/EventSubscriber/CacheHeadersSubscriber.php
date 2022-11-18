<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CacheHeadersSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['setHeaders']
        ];
    }


    public function setHeaders(ResponseEvent $event): void
    {
        $route = $event->getRequest()->attributes->get('_route');

        if ($route != '_api_/job_offers{._format}_get_collection'
            && $route != '_api_/users{._format}_get_collection') {
            return;
        }


        $query = $event->getRequest()->getQueryString();
        $response = $event->getResponse();

        if (strpos($query, 'order=for-you') === 0) {
            $response->setMaxAge(10800);
            $response->setSharedMaxAge(10800);
            $response->setVary(['Authorization']);
        } else if (strpos($query, 'order=newest') === 0) {
            $response->setMaxAge(3600);
            $response->setSharedMaxAge(3600);
        } else {
            $response->setMaxAge(86400);
            $response->setSharedMaxAge(86400);
        }

        $event->setResponse($response);
    }
}