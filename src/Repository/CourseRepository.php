<?php

namespace App\Repository;

use App\Entity\Course;
use App\Entity\Trainee;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
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

    /**
     * Search courses by title or keywords
     * 
     * @param string $search The search term
     * @return Course[] The list of courses
     * 
     */
    public function searchCourses(string $search): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.title LIKE :q OR c.keywords LIKE :q')
            ->setParameter('q', '%' . $search . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get the list of courses modules for a trainee
     * 
     * @param string $userId The trainer identifier
     * @return Course[] The list of courses
     */
    function getCoursesModulesBySector(string $userId): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.trainer', 't')
            ->innerJoin('c.module', 'cm')
            ->addSelect('cm', 't')
            ->where('t.username = :username')
            ->setParameter('username', $userId)
            ->groupBy('cm.id')
            ->orderBy('cm.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get the list of courses modules for a trainee
     * 
     * @param string $userId The trainee identifier
     * @return Course[] The list of courses
     */
    function getCoursesModulesByCohort(string $userId): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.trainer', 't')
            ->innerJoin('c.module', 'cm')
            ->innerJoin('c.courseTrainees', 'ct')
            ->innerJoin('ct.trainee', 'tr')
            ->innerJoin('tr.cohort', 'co')
            ->innerJoin('co.courseCohorts', 'cc', 'WITH', 'c.id = cc.course AND co.id = cc.cohort AND cc.active = 1')
            ->addSelect('cm', 't', 'co', 'ct', 'tr')
            ->where('tr.username = :username')
            ->setParameter('username', $userId)
            ->groupBy('cm.id')
            ->orderBy('cm.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get the list of latest courses for a trainee
     * 
     * @param string $idTrainee The trainee identifier
     * @return Course[] The list of courses
     */
    public function getLatestCoursesByTrainee(string $idTrainee): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.courseTrainees', 'ct')
            ->innerJoin('c.trainer', 't')
            ->innerJoin('ct.trainee', 'tr')
            ->innerJoin('tr.cohort', 'co')
            ->innerJoin('co.courseCohorts', 'cc', 'WITH', 'c.id = cc.course AND co.id = cc.cohort AND cc.active = 1')
            ->select('c', 'tr', 'ct', 't')
            ->where('tr.username = :traineeId')
            ->setParameter('traineeId', $idTrainee)
            ->orderBy('ct.date', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get the list of popular courses for a user
     * 
     * @param string $userId The user identifier
     * @return Course[] The list of courses
     */
    public function getPopularCoursesSector(string $userId): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.module', 'm')
            ->innerJoin('c.trainer', 't')
            ->addSelect('m', 't')
            ->where('t.username = :username')
            ->andWhere('c.visitors > 0')
            ->setParameter('username', $userId)
            ->setMaxResults(10)
            ->orderBy('c.visitors', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get the list of popular courses for a user
     * 
     * @param string $userId The user identifier
     * @return Course[] The list of courses
     */
    public function getPopularCoursesCohort(string $userId): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.courseTrainees', 'ct')
            ->innerJoin('ct.trainee', 'tr')
            ->innerJoin('tr.cohort', 'co')
            ->innerJoin('co.courseCohorts', 'cc', 'WITH', 'c.id = cc.course AND co.id = cc.cohort AND cc.active = 1')
            ->addSelect('ct', 'co', 'tr')
            ->where('tr.username = :username')
            ->andWhere('c.visitors > 0')
            ->setParameter('username', $userId)
            ->setMaxResults(10)
            ->orderBy('c.visitors', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get the list of courses informations
     * 
     * @param string $uuid The course uuid
     * @param string $search The search term
     * @return Course[] The list of courses
     */
    public function getCoursesInformations(string $uuid, string $search = null): array
    {
        $qb = $this->createQueryBuilder('c')
            ->innerJoin('c.module', 'cm')
            ->where('cm.uuid = :uuid')
            ->setParameter('uuid', $uuid);

        if ($search !== null) {
            $qb->andWhere('cm.label = :search OR c.title = :search OR c.keywords = :search')
                ->setParameter('search', $search);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Get the course informations
     * 
     * @param string $link The course link
     * @return Course|null The course informations
     */
    public function getCourseInformations(string $link): ?Course
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.module', 'cm')
            ->addSelect('cm')
            ->where('c.link = :link')
            ->setParameter('link', $link)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // TODO: terminer cette fonction
    public function homeworksToDo(string $userId)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('App\Entity\CourseCohort', 'cc', 'WITH', 'cc.course = c.id')
            ->innerJoin('App\Entity\TraineeResource', 'tr', 'WITH', 'tr.course = c.id')
            ->innerJoin('App\Entity\Trainee', 't', 'WITH', 't.id = tr.trainee')
            ->innerJoin('App\Entity\User', 'u', 'WITH', 'u.id = t.user')
            ->where('u.username = :username')
            ->andWhere('cc.active = true')
            ->andWhere('tr.id = NULL')
            ->setParameter('username', $userId)
            ->getQuery()
            ->getResult();
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
