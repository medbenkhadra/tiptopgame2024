<?php

namespace App\Repository;

use App\Entity\SocialMediaAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SocialMediaAccount>
 *
 * @method SocialMediaAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method SocialMediaAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method SocialMediaAccount[]    findAll()
 * @method SocialMediaAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SocialMediaAccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SocialMediaAccount::class);
    }

//    /**
//     * @return SocialMediaAccount[] Returns an array of SocialMediaAccount objects
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

//    public function findOneBySomeField($value): ?SocialMediaAccount
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
