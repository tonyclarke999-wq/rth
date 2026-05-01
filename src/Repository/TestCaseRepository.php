<?php

namespace App\Repository;

use App\Entity\TestCase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TestCase>
 *
 * @method TestCase|null find($id, $lockMode = null, $lockVersion = null)
 * @method TestCase|null findOneBy(array $criteria, array $orderBy = null)
 * @method TestCase[]    findAll()
 * @method TestCase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestCaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TestCase::class);
    }
}
