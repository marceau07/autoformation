<?php

namespace App\Repository;

use App\Entity\Course;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Course>
 *
 * @method Course|null find($id, $lockMode = null, $lockVersion = null)
 * @method Course|null findOneBy(array $criteria, array $orderBy = null)
 * @method Course[]    findAll()
 * @method Course[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    public function getLastCourses(string $idTrainee): array
    {
        $bdd = $this->getEntityManager()->getConnection();

        $query = $bdd->executeQuery(
            'SELECT c.*, u1.*, u2.* FROM course c INNER JOIN course_trainee ct INNER JOIN user u1 INNER JOIN user u2 WHERE (u1.id = c.trainer_id AND c.id = ct.course_id AND ct.trainee_id = u2.id AND u2.username=:username)',
            ['username' => $idTrainee]
        );

        return $query->fetchAllAssociative();
    }

    public function getPopularCourses(): array
    {
        $bdd = $this->getEntityManager()->getConnection();

        $query = $bdd->executeQuery(
            'SELECT c.*,t.*,u.*,cm.* FROM course c INNER JOIN course_module cm INNER JOIN trainer t INNER JOIN user u WHERE (cm.id = c.module_id AND t.id = c.trainer_id AND t.id = u.id) ORDER BY c.visitors'
        );
        return $query->fetchAllAssociative();
    }

    public function getCoursesInformations(string $uuid, string $search = null): array
    {
        $bdd = $this->getEntityManager()->getConnection();

        $query = $bdd->executeQuery(
            'SELECT c.*,t.*,u.*,cm.* FROM course c INNER JOIN course_module cm INNER JOIN trainer t INNER JOIN user u WHERE (cm.id = c.module_id AND t.id = c.trainer_id AND t.id = u.id AND cm.uuid=:uuid)',
            ['uuid' => $uuid]
        );
        return $query->fetchAllAssociative();
    }

    public function getCourseInformations(string $link): array
    {
        $bdd = $this->getEntityManager()->getConnection();

        $query = $bdd->executeQuery(
            'SELECT c.*,cm.* FROM course c INNER JOIN course_module cm WHERE (cm.id = c.module_id AND c.link=:link)',
            ['link' => $link]
        );
        return $query->fetchAssociative();
    }

    //    /**
    //     * @return Course[] Returns an array of Course objects
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

    //    public function findOneBySomeField($value): ?Course
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
