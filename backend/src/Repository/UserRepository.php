<?php

namespace App\Repository;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

//findUsersOnRole

    public function findUsersOnRole($user , $storeId) {
        $userRole = $user->getRoles()[0];
        $qb = $this->createQueryBuilder('u');


        if($userRole == 'ROLE_ADMIN') {
            if($storeId) {
                $qb->innerJoin('u.stores', 's')
                    ->andWhere('s.id = :store')
                    ->setParameter('store', $storeId);
            }
        }

        if ($userRole == 'ROLE_STOREMANAGER') {
            $qb->innerJoin('u.stores', 's')
                    ->andWhere('s.id = :store')
                    ->setParameter('store', $user->getStores()[0]);
        }

        if ($userRole == 'ROLE_EMPLOYEE') {
            $qb->innerJoin('u.tickets', 't')
                    ->andWhere('t.employee = :employee')
                    ->setParameter('employee', $user);
        }




        return $qb
            ->getQuery()
            ->getResult();
    }


    public function checkClientActivationTokenValidity($email , $token): bool
    {
        $user = $this->findOneBy(['email' => $email]);
        $userToken = $user->getToken();
        $userTokenExpiredAt = $user->getTokenExpiredAt();

        if($userToken == $token && $userTokenExpiredAt > new \DateTime()) {
            return true;
        } else {
            return false;
        }
    }

    public function findUniqueParticipants(): array
    {
        $roleClient = $this->_em->getRepository(Role::class)->findOneBy(['name' => 'ROLE_CLIENT']);
        $qb = $this->createQueryBuilder('u');

        $qb->innerJoin('u.tickets', 't')
            ->andWhere('u.role = :role')
            ->setParameter('role', $roleClient);

        return $qb->getQuery()->getResult();
    }



    public function activateUserAccount($email): void
    {
        $user = $this->findOneBy(['email' => $email]);
        $user->setIsActive(true);
        $user->setActivitedAt(new \DateTime());
        $user->setToken(null);
        $user->setTokenExpiredAt(null);
        $this->_em->persist($user);
        $this->_em->flush();
    }

}
