<?php

// src/EventListener/RequestListener.php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class RequestListener
{
    public function onKernelResponse(ResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            // don't do anything if it's not the master request
            return;
        }

        $response = $event->getResponse();

        // Set multiple headers simultaneously
        $response->headers->add([
            'Access-Control-Allow-Origin' => '*', // todo: chose in app the url
        ]);

    }
}