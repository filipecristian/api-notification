<?php

namespace App\Context;

class MarketingNotification extends AbstractContext
{
    public int $sentMaxInSeconds = 60 * 60;
    public int $qtdMaxSent = 3;
}