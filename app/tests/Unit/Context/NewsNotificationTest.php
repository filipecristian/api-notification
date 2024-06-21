<?php

namespace App\Tests\Unit\Context;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Context\NewsNotification;
use App\Bridge\ChannelInterface;
use App\Repository\NotificationRepository;
use App\Model\NotificationDto;
use App\Exception\RateLimitExceededException;

class NewsNotificationTest extends KernelTestCase
{
    public function testShouldSendNotification(): void
    {
        $channelMock = $this->createMock(ChannelInterface::class);
        $channelMock->expects($this->once())
            ->method('sendMessage');

        $repositoryMock = $this->createMock(NotificationRepository::class);
        $repositoryMock->expects($this->once())
            ->method('findLastsNotificationsByDate')
            ->willReturn([]);

        $dto = new NotificationDto(1, 'market', 'console', 'Hello', 'You are welcome.');
        

        $newsNotification = new NewsNotification($channelMock, $repositoryMock);
        $newsNotification->notify($dto);

        $this->assertEquals(86400, $newsNotification->sentMaxInSeconds);
        $this->assertEquals(1, $newsNotification->qtdMaxSent);
    }

    /**
     * @dataProvider getDataToLimitExceededException
     */
    public function testShouldThrowRateLimitExceededException($data): void
    {
        $this->expectException(RateLimitExceededException::class);

        $channelMock = $this->createMock(ChannelInterface::class);
        $channelMock->expects($this->never())
            ->method('sendMessage');

        $repositoryMock = $this->createMock(NotificationRepository::class);
        $repositoryMock->expects($this->once())
            ->method('findLastsNotificationsByDate')
            ->willReturn($data);

        $dto = new NotificationDto(1, 'market', 'console', 'Hello', 'You are welcome.');

        $newsNotification = new NewsNotification($channelMock, $repositoryMock);
        $newsNotification->notify($dto);
    }

    public function getDataToLimitExceededException(): array
    {
        return [
            [
                ['foo'],
                ['foo', 'bar'],
                ['foo', 'bar', 'fuzz'],
            ]
        ];
    }
}