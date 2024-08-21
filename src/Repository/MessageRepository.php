<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * Retrieve all messages between two users (one Trainer and one Trainee)
     * 
     * @param string $uuid      The UUID of the Trainer formatted to string
     * @param string $uuid2     The UUID of the Trainee formatted to string
     */
    public function getMessages(string $uuid, string $uuid2)
    {
        return $this->createQueryBuilder('m')
            ->innerJoin('App\Entity\Trainer', 't', 'WITH', 't.id = m.send_trainer OR t.id = m.trainer')
            ->innerJoin('App\Entity\Trainee', 't2', 'WITH', 't2.id = m.send_trainee OR t2.id = m.trainee')
            ->where('(t.uuid = :uuid AND t2.uuid = :uuid2) OR (t.uuid = :uuid2 AND t2.uuid = :uuid)')
            ->setParameter('uuid', $uuid)
            ->setParameter('uuid2', $uuid2)
            ->orderBy('m.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retrieve all messages between two users (both Trainers)
     * 
     * @param string $uuid      The UUID of the Trainer logged in formatted to string
     * @param string $uuid2     The UUID of the Trainer formatted to string
     */
    public function getMessagesBetweenTrainers(string $uuid, string $uuid2)
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('App\Entity\Trainer', 't', 'WITH', 't.id = m.send_trainer')
            ->leftJoin('App\Entity\Trainer', 't2', 'WITH', 't2.id = m.trainer')
            ->where('(t.uuid = :uuid AND t2.uuid = :uuid2) OR (t.uuid = :uuid2 AND t2.uuid = :uuid)')
            ->setParameter('uuid', $uuid)
            ->setParameter('uuid2', $uuid2)
            ->orderBy('m.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retrieve all messages between two users (both Trainees)
     * 
     * @param string $uuid      The UUID of the Trainee logged in formatted to string
     * @param string $uuid2     The UUID of the Trainee formatted to string
     */
    public function getMessagesBetweenTrainees(string $uuid, string $uuid2)
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('App\Entity\Trainee', 't', 'WITH', 't.id = m.send_trainee')
            ->leftJoin('App\Entity\Trainee', 't2', 'WITH', 't2.id = m.trainee')
            ->where('(t.uuid = :uuid AND t2.uuid = :uuid2) OR (t.uuid = :uuid2 AND t2.uuid = :uuid)')
            ->setParameter('uuid', $uuid)
            ->setParameter('uuid2', $uuid2)
            ->orderBy('m.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retrieve all messages between users and the cohort
     * 
     * @param string $uuid     The UUID of the Cohort formatted to string
     */
    public function getMessagesBetweenTraineesAndCohort(string $uuid)
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('App\Entity\Cohort', 'c', 'WITH', 'c.id = m.cohort')
            ->Where('c.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->orderBy('m.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    # TODO: Make this method retieving only the messages that are not readed for the current user in the current conversation 
    public function makeMessageReaded(int $idMessage)
    {
        return $this->createQueryBuilder('m')
            ->update('App\Entity\Message', 'm')
            ->set('m.readed', true)
            ->where('m.id=:idMessage')
            ->setParameter(':idMessage', $idMessage)
            ->getQuery()
            ->execute();
    }

    //    /**
    //     * @return Message[] Returns an array of Message objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Message
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
