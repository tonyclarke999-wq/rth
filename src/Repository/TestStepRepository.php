<?php

namespace App\Repository;

use App\Entity\TestStep;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TestStep>
 */
class TestStepRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TestStep::class);
    }

    public function findNextStepNumber(int $testCaseId): int
    {
        $result = $this->createQueryBuilder('t')
            ->select('MAX(t.stepNumber)')
            ->andWhere('t.testCase = :val')
            ->setParameter('val', $testCaseId)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (int) $result + 1 : 1;
    }
}
