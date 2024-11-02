<?php

namespace App\Repository;

use App\Entity\EmailTemplateVariable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmailTemplateVariable>
 *
 * @method EmailTemplateVariable|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailTemplateVariable|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailTemplateVariable[]    findAll()
 * @method EmailTemplateVariable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailTemplateVariableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailTemplateVariable::class);
    }

//    /**
//     * @return EmailTemplateVariable[] Returns an array of EmailTemplateVariable objects
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

//    public function findOneBySomeField($value): ?EmailTemplateVariable
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
