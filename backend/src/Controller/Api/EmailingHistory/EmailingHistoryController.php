<?php

namespace App\Controller\Api\EmailingHistory;


use App\Entity\EmailingHistory;

use App\Entity\Role;
use App\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;


class EmailingHistoryController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }



    public function getEmailingHistory(Request $request): JsonResponse
    {
        $store=  $request->get('store' , null);
        $role=  $request->get('role' , null);
        $user=  $request->get('user' , null);
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $start_date = $request->get('start_date', null);
        $end_date = $request->get('end_date', null);

        if ($start_date) {
            $start_date = DateTime::createFromFormat('d/m/Y', $start_date);
            $start_date->setTime(0, 0, 0);
            $start_date = $start_date->format('Y-m-d H:i:s');

        }

        if ($end_date) {
            $end_date = DateTime::createFromFormat('d/m/Y', $end_date);
            $end_date->setTime(23, 59, 59);
            $end_date = $end_date->format('Y-m-d H:i:s');
        }


        $query = $this->entityManager->createQueryBuilder()
            ->select('eh')
            ->from(EmailingHistory::class, 'eh')
            ->orderBy('eh.id', 'DESC');

        $emailingHistoryCount = count($query->getQuery()->getResult());

        $page = $page ?? 1;
        $pageSize = $limit ?? 10;

        $query->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize);


       if($store || $role){
           $query->innerJoin('eh.receiver', 'u');
       }

       if($store){
           $query
               ->innerJoin('u.stores', 's')
               ->andWhere('s.id = :store')
               ->setParameter('store' , $store);
       }



       if($role){
           $roleEntity = $this->entityManager->getRepository(Role::class)->findOneBy(['name' => $role]);

           $query
               ->andWhere('u.role = :role')
               ->setParameter('role' , $roleEntity);
       }

       

      if($user){
          $userEntity = $this->entityManager->getRepository(User::class)->find($user);

          $query->andWhere('eh.receiver = :user')
              ->setParameter('user' , $userEntity);
      }





        if ($start_date && $end_date) {
            $query->andWhere('eh.sent_at BETWEEN :start_date AND :end_date')
                ->setParameter('start_date', $start_date)
                ->setParameter('end_date', $end_date);
        } elseif ($start_date) {
            $query->andWhere('eh.sent_at >= :start_date')
                ->setParameter('start_date', $start_date);
        } elseif ($end_date) {
            $query->andWhere('eh.sent_at <= :end_date')
                ->setParameter('end_date', $end_date);
        }

        $emailingHistory = $query->getQuery()->getResult();

        $data = [];

        foreach ($emailingHistory as $item) {
            $data[] = $item->getEmailingHistoryJson();
        }

        return new JsonResponse([
            'emailingHistory' => $data,
            'emailingHistoryCount' => $emailingHistoryCount,
        ], Response::HTTP_OK);

    }


}
