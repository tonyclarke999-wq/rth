<?php

namespace App\Repository;

use App\Entity\Bug;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bug>
 *
 * @method Bug|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bug|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bug[]    findAll()
 * @method Bug[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BugRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bug::class);
    }

    /**
     * @return Bug[]
     */
    public function findByProjectAndSearch($project, string $query): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.project = :project')
            ->andWhere('b.summary LIKE :query OR b.description LIKE :query')
            ->setParameter('project', $project)
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('b.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
