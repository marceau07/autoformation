<?php

namespace App\Repository;

use App\Entity\CourseResource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CourseResource>
 *
 * @method CourseResource|null find($id, $lockMode = null, $lockVersion = null)
 * @method CourseResource|null findOneBy(array $criteria, array $orderBy = null)
 * @method CourseResource[]    findAll()
 * @method CourseResource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourseResourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CourseResource::class);
    }

    //    /**
    //     * @return CourseResource[] Returns an array of CourseResource objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CourseResource
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
