<?php

namespace App\Infrastructure\Queue\Handler;

use App\Domain\Live\LiveSyncService;
use App\Domain\Notification\NotificationService;
use App\Infrastructure\Mailing\Mailer;
use App\Infrastructure\Mailing\MailerService;
use App\Infrastructure\Queue\Message\ServiceMethodMessage;
use App\Infrastructure\Youtube\YoutubeUploaderService;
use Psr\Container\ContainerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class ServiceMethodMessageHandler implements MessageHandlerInterface, ServiceSubscriberInterface
{
    public function __construct(private readonly ContainerInterface $container)
    {
    }

    public function __invoke(ServiceMethodMessage $message): void
    {
        /** @var callable $callable */
        $callable = [
            $this->container->get($message->getServiceName()),
            $message->getMethod(),
        ];

        call_user_func_array($callable, $message->getParams());
    }

    public static function getSubscribedServices(): array
    {
        return [
            Mailer::class => Mailer::class
        ];
    }
}
