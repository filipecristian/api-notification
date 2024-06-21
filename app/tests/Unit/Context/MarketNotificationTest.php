<?php

namespace App\Tests\Unit\Context;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Context\MarketNotification;
use App\Bridge\ChannelInterface;
use App\Repository\NotificationRepository;
use App\Model\NotificationDto;
use App\Exception\RateLimitExceededException;

class MarketNotificationTest extends KernelTestCase
{
    public function testShouldSendNotification(): void
    {
        $channelMock = $this->createMock(ChannelInterface::class);
        $channelMock->expects($this->once())
            ->method('sendMessage');

        $repositoryMock = $this->createMock(NotificationRepository::class);
        $repositoryMock->expects($this->once())
            ->method('findLastsNotificationsByDate')
            ->willReturn(['foo', 'baar']);

        $dto = new NotificationDto(1, 'market', 'console', 'Hello', 'You are welcome.');
        
        $marketNotification = new MarketNotification($channelMock, $repositoryMock);
        $marketNotification->notify($dto);

        $this->assertEquals(3600, $marketNotification->sentMaxInSeconds);
        $this->assertEquals(3, $marketNotification->qtdMaxSent);
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

        $marketNotification = new MarketNotification($channelMock, $repositoryMock);
        $marketNotification->notify($dto);
    }

    public function getDataToLimitExceededException(): array
    {
        return [
            [
                ['foo', 'bar', 'fuzz'],
                ['foo', 'bar', 'fuzz', 'baz'],
                ['foo', 'bar', 'fuzz', 'buzz', 'foo'],
            ]
        ];
    }
}