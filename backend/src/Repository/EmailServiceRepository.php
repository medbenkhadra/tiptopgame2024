<?php

namespace App\Repository;

use App\Entity\EmailService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmailService>
 *
 * @method EmailService|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailService|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailService[]    findAll()
 * @method EmailService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailService::class);
    }

//    /**
//     * @return EmailService[] Returns an array of EmailService objects
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

//    public function findOneBySomeField($value): ?EmailService
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
