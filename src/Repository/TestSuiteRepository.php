<?php

namespace App\Repository;

use App\Entity\TestSuite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TestSuite>
 *
 * @method TestSuite|null find($id, $lockMode = null, $lockVersion = null)
 * @method TestSuite|null findOneBy(array $criteria, array $orderBy = null)
 * @method TestSuite[]    findAll()
 * @method TestSuite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestSuiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TestSuite::class);
    }
}
