<?php

namespace App\Repository;

use App\Entity\Coordinator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Coordinator>
 *
 * @method Coordinator|null find($id, $lockMode = null, $lockVersion = null)
 * @method Coordinator|null findOneBy(array $criteria, array $orderBy = null)
 * @method Coordinator[]    findAll()
 * @method Coordinator[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoordinatorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coordinator::class);
    }

    /**
     * @param string $search
     * @return Coordinator[]
     */
    public function searchCoordinators(string $search): array
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.username LIKE :q OR c.lastName LIKE :q OR c.firstName LIKE :q OR c.email LIKE :q')
            ->andWhere('c.roles LIKE :role')
            ->setParameter('q', '%' . $search . '%')
            ->setParameter('role', '%ROLE_COORDINATOR%')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Coordinator[] Returns an array of Coordinator objects
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

    //    public function findOneBySomeField($value): ?Coordinator
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
