<?php

namespace App\Repository;

use App\Entity\ClientFinalDraw;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ClientFinalDraw>
 *
 * @method ClientFinalDraw|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClientFinalDraw|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClientFinalDraw[]    findAll()
 * @method ClientFinalDraw[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientFinalDrawRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientFinalDraw::class);
    }

//    /**
//     * @return ClientFinalDraw[] Returns an array of ClientFinalDraw objects
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

//    public function findOneBySomeField($value): ?ClientFinalDraw
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
