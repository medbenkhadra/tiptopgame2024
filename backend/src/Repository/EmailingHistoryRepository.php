<?php

namespace App\Repository;

use App\Entity\EmailingHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmailingHistory>
 *
 * @method EmailingHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailingHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailingHistory[]    findAll()
 * @method EmailingHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailingHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailingHistory::class);
    }

//    /**
//     * @return EmailingHistory[] Returns an array of EmailingHistory objects
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

//    public function findOneBySomeField($value): ?EmailingHistory
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
