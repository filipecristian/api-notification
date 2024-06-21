<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use App\Exception\ChannelNotFoundException;
use App\Exception\ContextNotFoundException;
use App\Exception\RateLimitExceededException;
use App\Model\NotificationDto;
use App\Factory\NotificationFactory;
use Exception;

class NotificationController
{
    #[Route('/notification', methods: ['POST'])]
    public function send(
        #[MapRequestPayload] NotificationDto $notificationDto,
        NotificationFactory $notificationFactory
    ): Response {
        $response = new Response();
        try {
            $notification = $notificationFactory->get($notificationDto);
            $notification->notify($notificationDto);
            $response->setStatusCode(200);
            return $response->send();
        } catch (ChannelNotFoundException|ContextNotFoundException) {
            $response->setStatusCode(404);
            return $response->send();
        } catch (RateLimitExceededException) {
            $response->setStatusCode(429);
            return $response->send();
        } catch (Exception $ex) {
            $response->setStatusCode(500);
            $response->sendContent($ex->getMessage());
            return $response->send();
        }
    }
}
