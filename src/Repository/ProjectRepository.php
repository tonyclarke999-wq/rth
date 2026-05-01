<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    /**
     * @return Project[]
     */
    public function findActiveProjects(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isArchived = :val')
            ->setParameter('val', false)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Project[]
     */
    public function findBySearchQuery(string $query, bool $includeArchived = false): array
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.name LIKE :query OR p.description LIKE :query')
            ->setParameter('query', '%' . $query . '%');

        if (!$includeArchived) {
            $qb->andWhere('p.isArchived = :archived')
               ->setParameter('archived', false);
        }

        return $qb->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
