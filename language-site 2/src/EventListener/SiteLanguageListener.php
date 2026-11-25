<?php

namespace App\EventListener;

use App\Service\SiteLanguage;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::REQUEST, priority: 20)]
class SiteLanguageListener
{
    public function __construct(private readonly SiteLanguage $siteLanguage)
    {
    }

    public function __invoke(RequestEvent $event): void
    {

        $language = $this->siteLanguage->getLanguage();
        $event->getRequest()->attributes->set('_locale', $language);
        $event->getRequest()->setLocale($language);
    }
}
