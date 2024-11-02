<?php

namespace App\Controller\Api\ActionHistory;


use App\Entity\ActionHistory;

use App\Entity\Role;
use App\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;


class ActionsHistoryController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }



    public function getActionsHistory(Request $request): JsonResponse
    {
        $store=  $request->get('store' , null);
        $role=  $request->get('role' , null);
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
            ->select('ah')
            ->from(ActionHistory::class, 'ah')
            ->orderBy('ah.id', 'DESC');


        $actionsHistoryCount = count($query->getQuery()->getResult());


        $page = $page ?? 1;
        $pageSize = $limit ?? 10;
        $query->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize);


        if($store){
            $query->andWhere('ah.store = :store')
                ->setParameter('store', $store);
        }

        if($role){
            $roleEntity = $this->entityManager->getRepository(Role::class)->findOneBy(['name' => $role]);

            $query->innerJoin('ah.user_done_action', 'u1')
                ->leftJoin('ah.user_action_related_to', 'u2')
                ->andWhere('u1.role = :role OR u2.role = :role')
                ->setParameter('role' , $roleEntity);


        }


        if ($start_date && $end_date) {
            $query->andWhere('ah.created_at BETWEEN :start_date AND :end_date')
                ->setParameter('start_date', $start_date)
                ->setParameter('end_date', $end_date);
        } elseif ($start_date) {
            $query->andWhere('ah.created_at >= :start_date')
                ->setParameter('start_date', $start_date);
        } elseif ($end_date) {
            $query->andWhere('ah.created_at <= :end_date')
                ->setParameter('end_date', $end_date);
        }

        $actionsHistory = $query->getQuery()->getResult();

        $data = [];

        foreach ($actionsHistory as $actionHistory) {
            $data[] = $actionHistory->getActionHistoryJson();
        }

        return new JsonResponse([
            'actionsHistory' => $data,
            'actionsHistoryCount' => $actionsHistoryCount,
        ], Response::HTTP_OK);


    }



}
