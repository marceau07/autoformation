<?php

namespace App\Repository;

use App\Entity\Session;
use App\Entity\Trainee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trainee>
 *
 * @method Trainee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trainee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trainee[]    findAll()
 * @method Trainee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TraineeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trainee::class);
    }

    public function getSessionsInformations($idUser): array|bool
    {
        $bdd = $this->getEntityManager()->getConnection();

        $query = $bdd->executeQuery(
            'SELECT s.* FROM user u INNER JOIN trainee t INNER JOIN session s WHERE (u.username=:username AND u.id = t.id AND t.session_id = s.id)',
            ['username' => $idUser]
        );
        return $query->fetchAssociative();
    }

    public function updateCourseVisitors($slide): void
    {
        $bdd = $this->getEntityManager()->getConnection();

        $bdd->executeQuery(
            'UPDATE course SET visitors = visitors + 1 WHERE link=:link;',
            ['link' => $slide]
        );
    }

    public function updateCourseFollowed(string $slide, string $username): void
    {
        $bdd = $this->getEntityManager()->getConnection();

        $bdd->executeQuery(
            'REPLACE INTO course_trainee(trainee_id, course_id, date) VALUES((SELECT id FROM user WHERE username=:username), (SELECT id FROM course WHERE link=:link), NOW());',
            ['username' => $username, 'link' => $slide]
        );
    }

    //    /**
    //     * @return Trainee[] Returns an array of Trainee objects
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

    //    public function findOneBySomeField($value): ?Trainee
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
