<?php

namespace App\Bridge;

use App\Model\NotificationDto;

interface ChannelInterface
{
    public function sendMessage(NotificationDto $notificationDto): void;
}