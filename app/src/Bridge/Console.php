<?php

namespace App\Bridge;

use App\Model\NotificationDto;

class Console implements ChannelInterface
{
    public function sendMessage(NotificationDto $notificationDto): void
    {
        $message = sprintf(
            "Sending message to %s.\nTitle: %s\nContent: %s",
            $notificationDto->userId,
            $notificationDto->title,
            $notificationDto->message
        );

        echo $message;
    }
}