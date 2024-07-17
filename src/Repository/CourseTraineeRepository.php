<?php

namespace App\Repository;

use App\Entity\CourseTrainee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CourseTrainee>
 *
 * @method CourseTrainee|null find($id, $lockMode = null, $lockVersion = null)
 * @method CourseTrainee|null findOneBy(array $criteria, array $orderBy = null)
 * @method CourseTrainee[]    findAll()
 * @method CourseTrainee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourseTraineeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CourseTrainee::class);
    }
    
    //    /**
    //     * @return CourseTrainee[] Returns an array of CourseTrainee objects
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

    //    public function findOneBySomeField($value): ?CourseTrainee
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
