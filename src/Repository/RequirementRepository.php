<?php

namespace App\Repository;

use App\Entity\Requirement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Requirement>
 *
 * @method Requirement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Requirement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Requirement[]    findAll()
 * @method Requirement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequirementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Requirement::class);
    }

    /**
     * @return Requirement[]
     */
    public function findByProjectAndSearch($project, string $query): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.project = :project')
            ->andWhere('r.name LIKE :query OR r.content LIKE :query')
            ->setParameter('project', $project)
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('r.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
