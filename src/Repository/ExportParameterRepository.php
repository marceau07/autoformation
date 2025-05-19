<?php

namespace App\Repository;

use App\Entity\ExportParameter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExportParameter>
 * 
 * @method ExportParameter|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExportParameter|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExportParameter[]    findAll()
 * @method ExportParameter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExportParameterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExportParameter::class);
    }

    /**
     * @return ExportParameter|null
     */
    public function searchProspects(): ?ExportParameter
    {
        $query = $this->createQueryBuilder('e')
            ->where('e.dtype = :dtype')
            ->setParameter('dtype', 'prospect')
            ->getQuery();

        $result = $query->getOneOrNullResult();
        return $result;
    }

    //    /**
    //     * @return ExportParameter[] Returns an array of ExportParameter objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ExportParameter
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
