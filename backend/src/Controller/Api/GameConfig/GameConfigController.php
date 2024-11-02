<?php

namespace App\Controller\Api\GameConfig;


use App\Entity\GameConfig;

use App\Entity\User;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class GameConfigController extends AbstractController
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
    public function getGameConfig(Request $request): JsonResponse
    {
        $principalPeriod = 30;
        $validationPeriod = 60;

        $principalPeriodFinishAt=null;
        $validationPeriodFinishAt=null;

        $gameConfig = $this->entityManager->getRepository(GameConfig::class)->findAll();
        $gameConfig = $gameConfig[0] ?? null;
        $gameStatus = "";
        $timeRemainingToStart = [
            'days' => 0,
            'hours' => 0,
            'minutes' => 0,
            'seconds' => 0
        ];
        if ($gameConfig) {
            $startDate = $gameConfig->getStartDate() . " " . $gameConfig->getTime();
            $timeFormat = "d/m/Y H:i";
            $startDate = DateTime::createFromFormat($timeFormat, $startDate);
            $now = new DateTime();
            $interval = $startDate->diff($now);
            $timeRemainingToStart = [
                'days' => $interval->format('%a'),
                'hours' => $interval->format('%h'),
                'minutes' => $interval->format('%i'),
                'seconds' => $interval->format('%s')
            ];


            $startDateClone = clone $startDate;
            $principalPeriodFinishAt = $startDateClone->modify('+'.$principalPeriod.' days');

            $startDateClone = clone $startDate;
            $validationPeriodFinishAt = $startDateClone->modify('+'.$validationPeriod.' days');


            if($now < $startDate){
                $gameStatus = "A venir";
            }else if($now > $startDate && $now < $principalPeriodFinishAt){
                $gameStatus = "En cours";
                $interval = $principalPeriodFinishAt->diff($now);
                $timeRemainingToStart = [
                    'days' => $interval->format('%a'),
                    'hours' => $interval->format('%h'),
                    'minutes' => $interval->format('%i'),
                    'seconds' => $interval->format('%s')
                ];
            }else if($now > $principalPeriodFinishAt && $now < $validationPeriodFinishAt){
                $gameStatus = "Validation";
                $interval = $validationPeriodFinishAt->diff($now);
                $timeRemainingToStart = [
                    'days' => $interval->format('%a'),
                    'hours' => $interval->format('%h'),
                    'minutes' => $interval->format('%i'),
                    'seconds' => $interval->format('%s')
                ];
            }else if($now > $validationPeriodFinishAt){
                $gameStatus = "Terminé";
                $interval = $validationPeriodFinishAt->diff($now);
                $timeRemainingToStart = [
                    'days' => $interval->format('%a'),
                    'hours' => $interval->format('%h'),
                    'minutes' => $interval->format('%i'),
                    'seconds' => $interval->format('%s')
                ];
            }

        }

        $participantsCount = 0;
        $participants = $this->entityManager->getRepository(User::class)->findUniqueParticipants();
        if($participants){
            $participantsCount = count($participants);
        }



        return new JsonResponse([
            'gameConfig' => $gameConfig ? $gameConfig->getStartDate() : null,
            'principalPeriodFinishAt' => $this->getDateTimeAsJson($principalPeriodFinishAt),
            'validationPeriodFinishAt' => $this->getDateTimeAsJson($validationPeriodFinishAt),
            'timeRemainingToStart' => $timeRemainingToStart,
            'gameStatus' => $gameStatus,
            'time' => $gameConfig ? $gameConfig->getTime() : '00:00',
            'participantsCount' => $participantsCount
        ]);


    }

    /**
     * @isGranted("ROLE_ADMIN")
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function updateGameConfig(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $originalDate = new DateTime($data['startDate']);
        $formattedDate = $originalDate->format('d/m/Y');

        $gameConfig = $this->entityManager->getRepository(GameConfig::class)->findAll();
        $gameConfig = $gameConfig[0] ?? null;
        if ($gameConfig) {
            $gameConfig->setStartDate($formattedDate);
            $gameConfig->setTime($data['time']);
            $this->entityManager->persist($gameConfig);
            $this->entityManager->flush();
        } else {
            $gameConfig = new GameConfig();
            $gameConfig->setStartDate($formattedDate);
            $gameConfig->setTime($data['time']);
            $this->entityManager->persist($gameConfig);
            $this->entityManager->flush();
        }

        return new JsonResponse([
            'gameConfig' => $gameConfig->getStartDate()
        ]);
    }

    private function getDateTimeAsJson(DateTime|bool|null $principalPeriodFinishAt)
    {
        return [
            'date' => $principalPeriodFinishAt ? $principalPeriodFinishAt->format('d/m/Y') : '00/00/0000',
            'time' => $principalPeriodFinishAt ? $principalPeriodFinishAt->format('H:i') : '00:00'
        ];
    }


}
