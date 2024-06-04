<?php

namespace App\Repository;

use App\Entity\Faq;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Faq>
 *
 * @method Faq|null find($id, $lockMode = null, $lockVersion = null)
 * @method Faq|null findOneBy(array $criteria, array $orderBy = null)
 * @method Faq[]    findAll()
 * @method Faq[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FaqRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Faq::class);
    }

    public function getThemes($idSector = null): array|bool
    {
        $bdd = $this->getEntityManager()->getConnection();

        $query = $bdd->executeQuery(
            'SELECT s.*, f.* FROM sector s INNER JOIN faq f WHERE (s.id = f.sector_id) AND (sector_id=:idSector OR sector_id IS NULL) GROUP BY f.theme', 
            ['idSector' => $idSector]
        );
        return $query->fetchAllAssociative();
    }

    public function getFaqs($idSector = null): array|bool
    {
        $bdd = $this->getEntityManager()->getConnection();

        $query = $bdd->executeQuery(
            'SELECT s.*, f.* FROM sector s INNER JOIN faq f WHERE (s.id = f.sector_id) AND visibility IS TRUE AND (f.sector_id=:idSector OR f.sector_id IS NULL) ORDER BY f.sector_id, f.title', 
            ['idSector' => $idSector]
        );
        return $query->fetchAllAssociative();
    }

    //    /**
    //     * @return Faq[] Returns an array of Faq objects
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

    //    public function findOneBySomeField($value): ?Faq
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
