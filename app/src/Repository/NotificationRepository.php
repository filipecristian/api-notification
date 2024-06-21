<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Model\NotificationDto;
use DateTime;
use DateTimeImmutable;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
    * @return Notification[] Returns an array of Notification objects
    */
    public function findLastsNotificationsByDate(NotificationDto $notificationDto, int $max, DateTime $date): array
    {
        return $this->createQueryBuilder('notifications')
            ->andWhere('notifications.userId = :val')
            ->setParameter('val', $notificationDto->userId)
            ->andWhere('notifications.channel = :val_1')
            ->setParameter('val_1', $notificationDto->channel)
            ->andWhere('notifications.context = :val_2')
            ->setParameter('val_2', $notificationDto->context)
            ->andWhere('notifications.created_at > :val_3')
            ->setParameter('val_3', $date)
            ->orderBy('notifications.created_at', 'ASC')
            ->setMaxResults($max)
            ->getQuery()
            ->getResult();
    }

    public function insert(NotificationDto $notificationDto)
    {
        $entityManager = $this->getEntityManager();
        $notification = new Notification;
        $notification->setUserId($notificationDto->userId);
        $notification->setContext($notificationDto->context);
        $notification->setChannel($notificationDto->channel);
        $notification->setTitle($notificationDto->title);
        $notification->setMessage($notificationDto->message);
        $notification->setCreatedAt(DateTimeImmutable::createFromMutable(new DateTime()));

        $entityManager->persist($notification);
        $entityManager->flush();
    }
}
