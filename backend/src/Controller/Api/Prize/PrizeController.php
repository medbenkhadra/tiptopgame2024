<?php

namespace App\Controller\Api\Prize;


use App\Entity\Prize;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class PrizeController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllPrizes(Request $request): JsonResponse
    {
        $prizes = $this->entityManager->getRepository(Prize::class)->findAll();
        $data = [];
        $prizesJson= [];
        foreach ($prizes as $prize) {
            $prizesJson[] =
                $prize->getPrizeJson();
        }

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('count(p.id) As count , p.label')
            ->from(Prize::class, 'p')
            ->leftJoin('p.tickets', 't')
            ->groupBy('p.id');


        $counters = $qb->getQuery()->getResult();

        $index=0;
        foreach ($prizes as $prize) {
            $prizesJson[$prize->getPrizeJson()['label']] =
                $prize->getPrizeJson();
        };

        $totalItems = array_reduce($counters, function ($sum, $counter) {
            return $sum + $counter['count'];
        }, 0);

        $formattedJson = [];
        foreach ($prizes as $prize) {
            $index++;
            $percentage = ($counters[$index - 1]['count'] / $totalItems) * 100;
            $roundedPercentage = number_format($percentage, 0);
            $formattedJson[] =
               [
                   'id' => $prize->getId(),
                   'label' => $prize->getLabel(),
                   'name' => $prize->getName(),
                   'type' => $prize->getType(),
                   'prize_value' => $prize->getPrizeValue(),
                   'winning_rate' => $prize->getWinningRate(),
                   'totalCount' => $counters[$index-1]['count'],
                   'percentage' => $roundedPercentage,
               ];
        }



        return $this->json([
            'prizes' => $formattedJson,
            'status' => 'success',

        ], 200);


    }


}
