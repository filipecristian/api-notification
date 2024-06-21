<?php

namespace App\Context;

class StatusNotification extends AbstractContext
{
    public int $sentMaxInSeconds = 60;
    public int $qtdMaxSent = 2;
}