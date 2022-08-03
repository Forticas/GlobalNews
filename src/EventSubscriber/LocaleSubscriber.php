<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

class LocaleSubscriber implements EventSubscriberInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onKernelRequest(RequestEvent $event): void
    {

        $request = $event->getRequest();

/*
        if (!$request->hasPreviousSession()) {
            if ($request->getPathInfo() === '/') {
                // check if HTTP_ACCEPT_LANGUAGE is defined and if containes a language existin between fr and en
                if ($request->headers->has('Accept-Language') && strpos($request->headers->get('Accept-Language'), 'fr') !== false) {
                    $request->setLocale('fr');
                    $request->getSession()->set('_locale', 'fr');
                } else {
                    $request->setLocale('en');
                    $request->getSession()->set('_locale', 'en');
                }

                $url = $this->router->generate('app_index', ['_locale' => $request->getLocale()]);
                $event->setResponse(new RedirectResponse($url));
            }
            return;
        }
*/
        if ($locale = $request->attributes->get('_locale')) {

            $request->getSession()->set('_locale', $locale);
            //$request->setLocale($locale);
        } else {
            $request->setLocale($request->getSession()->get('_locale', $request->getDefaultLocale()));
        }
/*
        if ($request->getPathInfo() === '/') {
            $url = $this->router->generate('app_index', ['_locale' => $request->getLocale()]);
            $event->setResponse(new RedirectResponse($url));
        }
*/
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 20],
        ];
    }
}
