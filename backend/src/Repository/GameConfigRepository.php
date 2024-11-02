<?php

namespace App\Repository;

use App\Entity\GameConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameConfig>
 *
 * @method GameConfig|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameConfig|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameConfig[]    findAll()
 * @method GameConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameConfigRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameConfig::class);
    }

//    /**
//     * @return GameConfig[] Returns an array of GameConfig objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GameConfig
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
