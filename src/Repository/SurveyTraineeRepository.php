<?php

namespace App\Repository;

use App\Entity\SurveyTrainee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SurveyTrainee>
 *
 * @method SurveyTrainee|null find($id, $lockMode = null, $lockVersion = null)
 * @method SurveyTrainee|null findOneBy(array $criteria, array $orderBy = null)
 * @method SurveyTrainee[]    findAll()
 * @method SurveyTrainee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SurveyTraineeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SurveyTrainee::class);
    }

    public function getGlobalSurveys(): array|bool
    {
        $bdd = $this->getEntityManager()->getConnection();

        $query = $bdd->executeQuery(
            'SELECT st.*, a.*, u.* FROM survey_trainee st INNER JOIN user u INNER JOIN avatar a WHERE (u.id = st.trainee_id AND st.survey_id IS NOT NULL AND st.rate IS NOT NULL AND a.id = u.avatar_id)', 
        );
        return $query->fetchAllAssociative();
    }

    //    /**
    //     * @return SurveyTrainee[] Returns an array of SurveyTrainee objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?SurveyTrainee
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
