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
     * Retrieve all messages between two users
     * 
     * @param string $uuid      The UUID of the People logged in formatted to string
     * @param string $uuid2     The UUID of the People formatted to string
     */
    public function getMessagesBetweenPeople(string $uuid, string $uuid2)
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('App\Entity\User', 'u', 'WITH', 'u.id = m.send_people')
            ->leftJoin('App\Entity\User', 'u2', 'WITH', 'u2.id = m.people')
            ->where('(u.uuid = :uuid AND u2.uuid = :uuid2) OR (u.uuid = :uuid2 AND u2.uuid = :uuid)')
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

    # TODO: Make this method retrieving only the messages that are not readed for the current user in the current conversation 
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

    /**
     * Récupérer les conversations récentes pour un utilisateur donné.
     */
    public function findRecentConversations($user): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.send_trainer = :user OR m.send_trainee = :user OR m.trainee = :user OR m.trainer = :user')
            ->setParameter('user', $user)
            ->orderBy('m.date', 'DESC')
            ->getQuery()
            ->getResult();
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
