<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class KernelListener
{

    /**
     * KernelListener constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        // get request
        $request = $event->getRequest();

        // si locale dans cookie on prend, si dans session on prend, sinon c'est en
        $cookie = $request->cookies;
        if ($cookie->has('locale')){
            $locale = $cookie->get('locale');
        }else{
            $locale = $request->getSession()->get('_locale', 'en');
        }
        $request->setLocale($locale);
    }

}