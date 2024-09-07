<?php

namespace App\Repository;

use App\Entity\CourseModule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CourseModule>
 *
 * @method CourseModule|null find($id, $lockMode = null, $lockVersion = null)
 * @method CourseModule|null findOneBy(array $criteria, array $orderBy = null)
 * @method CourseModule[]    findAll()
 * @method CourseModule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourseModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CourseModule::class);
    }

    //    /**
    //     * @return CourseModule[] Returns an array of CourseModule objects
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

    //    public function findOneBySomeField($value): ?CourseModule
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
