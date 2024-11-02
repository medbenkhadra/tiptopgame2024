<?php

namespace App\Repository;

use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ticket>
 *
 * @method Ticket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ticket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ticket[]    findAll()
 * @method Ticket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    public function findByDateAndStore($startDate, $endDate, $store,$user)
    {
        $startDate = \DateTime::createFromFormat('d/m/Y', $startDate)->format('Y-m-d H:i:s');
        $endDate = \DateTime::createFromFormat('d/m/Y', $endDate)->format('Y-m-d H:i:s');

        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.updated_at BETWEEN :startDate AND :endDate')
            ->andWhere('t.updated_at IS NOT NULL')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);

        if ($store) {
            $qb->andWhere('t.store = :store')
                ->setParameter('store', $store);
        }

        $userRoles = $user->getRoles();
        if (in_array('ROLE_CLIENT', $userRoles)) {
            $qb->andWhere('t.user = :user')
                ->setParameter('user', $user);
        }

        if (in_array('ROLE_STOREMANAGER', $userRoles)) {
            $qb->andWhere('t.store = :store')
                ->setParameter('store', $user->getStores()[0]);
        }

        if (in_array('ROLE_EMPLOYEE', $userRoles)) {
            $qb->andWhere('t.employee = :employee')
                ->setParameter('employee', $user);
        }




        return $qb
            ->getQuery()
            ->getResult();
    }


    public function findTicketsRelatedToUser($user, $startDate, $endDate, $storeId , $userRole)
    {
        $oneYearAgo = new \DateTime();
        $oneYearAgo->modify('-1 year');


        $startDate = $startDate ?  \DateTime::createFromFormat('d/m/Y', $startDate)->format('Y-m-d H:i:s') : $oneYearAgo->format('Y-m-d H:i:s');
        $endDate = $endDate ? \DateTime::createFromFormat('d/m/Y', $endDate)->format('Y-m-d H:i:s') : (new \DateTime())->format('Y-m-d H:i:s');

        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.updated_at BETWEEN :startDate AND :endDate')
            ->orWhere('t.ticket_generated_at BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);


        $userRole = $user->getRoles()[0];
        if ($userRole == 'ROLE_CLIENT') {
            $qb->andWhere('t.user = :user')
                ->setParameter('user', $user);
        }

        if ($userRole == 'ROLE_STOREMANAGER') {
            $qb->andWhere('t.store = :store')
                ->setParameter('store', $user->getStores()[0]);
        }

        if ($userRole == 'ROLE_EMPLOYEE') {
            $qb->andWhere('t.employee = :employee')
                ->setParameter('employee', $user);
        }

        if($userRole == 'ROLE_ADMIN'){
            if($storeId){
                $qb->andWhere('t.store = :store')
                    ->setParameter('store', $storeId);
            }
        }



        return $qb
            ->getQuery()
            ->getResult();
    }


    /**
     * @param array $tickets
     * @return array
     */
    public function getTicketCountByStatus(array $tickets): array
    {
        $ticketCounter = [];
        foreach ($tickets as $ticket) {
            $ticketCounter[$ticket->getStatus()][] = $ticket;
        }
        return $ticketCounter;
    }
}
