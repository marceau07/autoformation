<?php

namespace App\Repository;

use App\Entity\TraineeResource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TraineeResource>
 */
class TraineeResourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TraineeResource::class);
    }

    public function findByTrainee(string $userId) {
        return $this->createQueryBuilder('tr')
            ->select('tr')
            ->innerJoin('tr.trainee', 't')
            ->innerJoin('App\Entity\User', 'u', 'u.id = t.id')
            ->andWhere('u.username = :username')
            ->setParameter('username', $userId)
            ->getQuery()
            ->getResult()
        ;
    }

    //    /**
    //     * @return TraineeResource[] Returns an array of TraineeResource objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?TraineeResource
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
