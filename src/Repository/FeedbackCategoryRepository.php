<?php

namespace App\Repository;

use App\Entity\FeedbackCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FeedbackCategory>
 *
 * @method FeedbackCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method FeedbackCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method FeedbackCategory[]    findAll()
 * @method FeedbackCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeedbackCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FeedbackCategory::class);
    }

    //    /**
    //     * @return FeedbackCategory[] Returns an array of FeedbackCategory objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?FeedbackCategory
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
