<?php

namespace App\Controller\Api\ConnectionHistory;


use App\Entity\ConnectionHistory;

use App\Entity\Role;
use App\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;


class ConnectionHistoryController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }



    public function getConnectionsHistory(Request $request): JsonResponse
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
            ->select('ch')
            ->from(ConnectionHistory::class, 'ch')
            ->orderBy('ch.id', 'DESC');

        $connectionsHistoryCount = count($query->getQuery()->getResult());

        $page = $page ?? 1;
        $pageSize = $limit ?? 10;

        $query->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize);

        if($store || $role) {
            $query->innerJoin('ch.user', 'u');
        }


        if($role){
            $roleEntity = $this->entityManager->getRepository(Role::class)->findOneBy(['name' => $role]);

            $query
                ->andWhere('u.role = :role')
                ->setParameter('role' , $roleEntity);
        }

        if($user){
            $userEntity = $this->entityManager->getRepository(User::class)->find($user);

            $query->andWhere('ch.user = :user')
                ->setParameter('user' , $userEntity);
        }

        if ($start_date && $end_date) {
            $query->andWhere('ch.logout_time BETWEEN :start_date AND :end_date')
                ->setParameter('start_date', $start_date)
                ->setParameter('end_date', $end_date);
        } elseif ($start_date) {
            $query->andWhere('ch.logout_time >= :start_date')
                ->setParameter('start_date', $start_date);
        } elseif ($end_date) {
            $query->andWhere('ch.logout_time <= :end_date')
                ->setParameter('end_date', $end_date);
        }

        $connectionsHistory = $query->getQuery()->getResult();

        $data = [];

        foreach ($connectionsHistory as $connectionHistory) {
            $data[] = $connectionHistory->getConnectionHistoryJson();
        }

        return new JsonResponse([
            'connectionsHistory' => $data,
            'connectionsHistoryCount' => $connectionsHistoryCount,
        ], Response::HTTP_OK);


    }



}
