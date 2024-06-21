<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class NotificationDto 
{
    public function __construct(
        #[Assert\NotBlank]
        public int $userId,
        #[Assert\NotBlank]
        public string $context,
        #[Assert\NotBlank]
        public string $channel,
        #[Assert\NotBlank]
        public string $title,
        #[Assert\NotBlank]
        public string $message,
    ) {
    }
}
