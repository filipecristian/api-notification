<?php

namespace App\Context;

use App\Model\NotificationDto;
use App\Bridge\ChannelInterface;
use App\Exception\RateLimitExceededException;
use App\Repository\NotificationRepository;
use DateTime;

abstract class AbstractContext
{
    public int $sentMaxInSeconds = 0;
    public int $qtdMaxSent = 0;

    public function __construct(public ChannelInterface $channel, public NotificationRepository $notificationRepository)
    {
    }

    public function notify(NotificationDto $notificationDto): void
    {
        if (!$this->canNotify($notificationDto)) {
            throw new RateLimitExceededException();
        }
        
        $this->channel->sendMessage($notificationDto);
        
        $this->notificationRepository->insert($notificationDto);
    }

    protected function canNotify(NotificationDto $notificationDto): bool
    {
        $date = new DateTime();
        $date->modify(sprintf('-%s seconds', $this->sentMaxInSeconds));

        $data = $this->notificationRepository->findLastsNotificationsByDate($notificationDto, $this->sentMaxInSeconds, $date);
        
        if (count($data) >= $this->qtdMaxSent) {
            return false;
        }
        return true;
    }
}
