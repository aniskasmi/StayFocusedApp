<?php

namespace App\Domain\Billing\Repository;

use App\Domain\Billing\Entity\Plan;
use App\Infrastructure\Orm\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Plan>
 */
class PlanRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plan::class);
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('plan')
            ->orderBy('plan.duration', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
