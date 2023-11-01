<?php

declare(strict_types=1);

namespace App\Domain\Billing\Subscriber;

use App\Domain\Auth\Event\TestEvent;
use App\Domain\Billing\Exception\BillingNotBanException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TestSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            TestEvent::class => 'onTest',
        ];
    }

    public function onTest(TestEvent $event)
    {
        return 'COUCOU';
    }
}
