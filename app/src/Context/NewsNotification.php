<?php

namespace App\Context;

class NewsNotification extends AbstractContext
{
    public int $sentMaxInSeconds = 60 * 60 * 24;
    public int $qtdMaxSent = 1;
}