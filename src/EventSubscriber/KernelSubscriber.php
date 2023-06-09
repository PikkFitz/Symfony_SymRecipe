<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class KernelSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [RequestEvent::class => 'onKernelRequest'];
            // ['eventName' => 'methodName']
    }


    public function onKernelRequest(RequestEvent $request)
    {
        //dd($event->getRequest()->getPathInfo());
    }
}