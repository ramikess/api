<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Book;
use App\Entity\Loan;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
final class TimestampListener
{
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Book && !$entity instanceof Loan) {
            return;
        }

        $now = new \DateTimeImmutable();
        $entity->setCreatedAt($now);
        $entity->setUpdatedAt($now);
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Book && !$entity instanceof Loan) {
            return;
        }

        $entity->setUpdatedAt(new \DateTimeImmutable());

        $em = $args->getObjectManager();
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet(
            $em->getClassMetadata($entity::class),
            $entity,
        );
    }
}
