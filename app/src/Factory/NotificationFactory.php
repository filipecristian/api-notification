<?php

namespace App\Factory;

use App\Model\NotificationDto;
use App\Context\AbstractContext;
use App\Context\StatusNotification;
use App\Context\NewsNotification;
use App\Context\MarketingNotification;
use App\Bridge\ChannelInterface;
use App\Bridge\Console;
use App\Repository\NotificationRepository;
use App\Exception\ChannelNotFoundException;
use App\Exception\ContextNotFoundException;

class NotificationFactory
{
    public function __construct(public NotificationRepository $notificationRepository)
    {
    }

    public function get(NotificationDto $notificationDto): AbstractContext
    {
        $channel = $this->getChannel($notificationDto->channel);
        return $this->getContext($notificationDto->context, $channel);
    }

    private function getChannel(string $channel): ChannelInterface
    {
        return match ($channel) {
            'console' => new Console,
            default => throw new ChannelNotFoundException()
        };
    }

    private function getContext(string $context, ChannelInterface $channel): AbstractContext
    {
        return match ($context) {
            'status' => new StatusNotification($channel, $this->notificationRepository),
            'news' => new NewsNotification($channel, $this->notificationRepository),
            'marketing' => new MarketingNotification($channel, $this->notificationRepository),
            default => throw new ContextNotFoundException()
        };
    }
}
