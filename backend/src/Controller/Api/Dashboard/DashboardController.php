<?php

namespace App\Controller\Api\Dashboard;

use App\Entity\Prize;
use App\Entity\Ticket;
use App\Entity\User;
use DateTime;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Exception as DBALException;
class DashboardController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }


    public function getClientDashboardCounters(): JsonResponse
    {
        $user = $this->getUser();
        $tickets = $user->getTickets();

        $counters = [
            'tickets' => count($tickets),
            'playedTickets' => 0,
            'confirmedTickets' => 0,
            'pendingTickets' => 0,
            'loyaltyPoints' => 0,
        ];

        foreach ($tickets as $ticket) {
            if ($ticket->getStatus() == Ticket::STATUS_PENDING_VERIFICATION || $ticket->getStatus() == Ticket::STATUS_WINNER) {
                $counters['playedTickets']++;
            }
            if ($ticket->getStatus() == Ticket::STATUS_WINNER) {
                $counters['confirmedTickets']++;
            }
            if ($ticket->getStatus() == Ticket::STATUS_PENDING_VERIFICATION) {
                $counters['pendingTickets']++;
            }
        }

        $loyaltyPoints = $user->getLoyaltyPoints();

        foreach ($loyaltyPoints as $loyaltyPoint) {
            $counters['loyaltyPoints'] += $loyaltyPoint->getPoints();
        }


        return $this->json([
            'counters' => $counters,
        ]);
    }


    public function getAdminDashboardCounters(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $startDate = $data['startDate'] ?? null;
        $endDate = $data['endDate'] ?? null;
        $storeId = $data['storeId'] ?? null;


        $counters = [
            'tickets' => 0,
            'printedTickets' => 0,
            'ticketStock' => 0,
            'clients' => 0,
            'participants' => 0,
            'playedTicket' => 0,
            "confirmedTickets" => 0,
        ];


        $user = $this->getUser();
        $userRole = $user->getRoles()[0];

        $tickets = $this->entityManager->getRepository(Ticket::class)->findTicketsRelatedToUser($user, $startDate, $endDate, $storeId, $userRole);
        $counters['tickets'] = count($tickets);

        foreach ($tickets as $ticket) {


            if ($ticket->getStatus() != Ticket::STATUS_GENERATED) {
                $counters['printedTickets']++;
            }

            if ($ticket->getStatus() == Ticket::STATUS_PENDING_VERIFICATION || $ticket->getStatus() == Ticket::STATUS_WINNER) {
                $counters['playedTicket']++;
            }

            if ($ticket->getStatus() == Ticket::STATUS_WINNER) {
                $counters['confirmedTickets']++;
            }
        }

        $counters['ticketStock'] = $counters['tickets'] - $counters['printedTickets'];


        $users = $this->entityManager->getRepository(User::class)->findUsersOnRole($user, $storeId);

        foreach ($users as $user) {
            if ($user->getRoles()[0] == 'ROLE_CLIENT') {
                $counters['clients']++;
            }
            if ($user->getRoles()[0] == 'ROLE_CLIENT' && count($user->getTickets()) > 0) {
                $counters['participants']++;
            }
        }


        return $this->json([
            'counters' => $counters,
        ]);
    }

    public function getDashboardStats(Request $request): JsonResponse
    {

        $startDate = $request->get('startDate') ?? date('Y-m-d');
        $endDate = $request->get('endDate') ?? date('Y-m-d');
        $storeId = $request->get('storeId') ?? null;


        $user = $this->getUser();

        $tickets = $this->entityManager->getRepository(Ticket::class)->findByDateAndStore($startDate, $endDate, $storeId, $user);

        $counters = [
            'tickets' => count($tickets),
            'playedTickets' => 0,
            'confirmedTickets' => 0,
            'pendingTickets' => 0,
        ];

        foreach ($tickets as $ticket) {
            $ticketHistory = $ticket->getTicketHistories();
            $lastHistory = $ticketHistory[count($ticketHistory) - 1];

            if(!$lastHistory){
                continue;
            }

            if ($lastHistory->getStatus() == Ticket::STATUS_PENDING_VERIFICATION || $lastHistory->getStatus() == Ticket::STATUS_WINNER) {
                $counters['playedTickets']++;
            }
            if ($lastHistory->getStatus() == Ticket::STATUS_WINNER) {
                $counters['confirmedTickets']++;
            }
            if ($lastHistory->getStatus() == Ticket::STATUS_PENDING_VERIFICATION) {
                $counters['pendingTickets']++;
            }

        }


        $stats = [];

        $gainByAge = $this->getGainByAge($tickets);
        $stats['gainByAge'] = $gainByAge;

        $gainByGender = $this->getGainByGender($tickets);
        $stats['gainByGender'] = $gainByGender;

        $gainByPrize = $this->getGainByPrize($tickets);
        $stats['gainByPrize'] = $gainByPrize;

        $stats['gainByCity'] = $this->getGainByCity($tickets);
        $stats['gainByStores'] = $this->getGainByStores($tickets);

        $topGain = $this->getTopGain($tickets);
        $stats["participationTendance"] = $this->getParticipationTendance($tickets);

        $ticketsByStatuses = $this->getStatsByStatuses($tickets);
        $stats["ticketsByStatuses"] = $ticketsByStatuses;

        $stats["gainByGenderByAge"] = $this->getGainByGenderByAge($tickets);

        $stats["playGameTendance"] = $this->getPlayGameTendance($tickets);

        $stats["prizesCostTendance"] = $this->getPrizesCostTendance($tickets);

        $totalPrizesPrice = $this->getTotalPrizesPrice($tickets);


        return $this->json([
            'startDate' => $startDate,
            'endDate' => $endDate,
            'gameCount' => $counters['playedTickets'],
            'totalGainAmount' => $totalPrizesPrice,
            'stats' => $stats,
            'topGain' => $topGain,
        ]);
    }

    private function getGainByAge($tickets): array
    {
        $gainByAge = [
            0 => 0,
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
        ];
        $userIds = [];
        foreach ($tickets as $ticket) {
            $user = $ticket->getUser();
            if ($user && !in_array($user->getId(), $userIds)) {
                $userIds[] = $user->getId();
                $age = $user->getAge();
                if ($age >= 18 && $age <= 24) {
                    $gainByAge[0]++;
                }
                if ($age >= 25 && $age <= 34) {
                    $gainByAge[1]++;
                }
                if ($age >= 35 && $age <= 44) {
                    $gainByAge[2]++;
                }
                if ($age >= 45 && $age <= 54) {
                    $gainByAge[3]++;
                }
                if ($age >= 55) {
                    $gainByAge[4]++;
                }
            }

        }

        return $gainByAge;

    }

    private function getGainByGender($tickets): array
    {
        $stats = [];


        usort($tickets, function ($a, $b) {
            $prizeIdA = $a->getPrize()->getId();
            $prizeIdB = $b->getPrize()->getId();

            return $prizeIdA - $prizeIdB;
        });

        foreach ($tickets as $ticket) {
            $user = $ticket->getUser();
            $prizename = $ticket->getPrize()->getLabel();

            if ($user && $prizename) {
                $gender = strtolower($user->getGender());

                if (!isset($stats[$prizename])) {
                    $stats[$prizename] = [
                        "homme" => 0,
                        "femme" => 0,
                        "autre" => 0,
                    ];
                }

                $stats[$prizename][$gender]++;
            }
        }

        return $stats;
    }


    private function getGainByPrize($tickets): array
    {
        $prizes = $this->entityManager->getRepository(Prize::class)->findAll();
        $gainByPrize = [];
        foreach ($prizes as $prize) {
            $newKey = $prize->getLabel() ? str_replace(['"', "'"], '', $prize->getLabel()) : "";
            $gainByPrize[$newKey] = 0;

        }

        foreach ($tickets as $ticket) {
            $prize = $ticket->getPrize();
            if ($prize && $ticket->getStatus() == Ticket::STATUS_WINNER) {
                $newKey = $prize->getLabel() ? str_replace(['"', "'"], '', $prize->getLabel()) : "";
                $gainByPrize[$newKey]++;
            }
        }

        return $gainByPrize;
    }

    private function getGainByCity($tickets)
    {
        $stats = [];
        foreach ($tickets as $ticket) {
            $store = $ticket->getStore();
            if ($store) {
                $city = $store->getCity();
                if ($city) {
                    if (!isset($stats[$city])) {
                        $stats[$city] = 0;
                    }
                    $stats[$city]++;
                }
            }

        }

        return $stats;
    }

    private function getGainByStores($tickets)
    {
        $stats = [];


        foreach ($tickets as $ticket) {
            $store = $ticket->getStore();
            if ($store) {
                $storeName = $store->getName();
                if ($storeName) {
                    if (!isset($stats[$storeName])) {
                        $stats[$storeName] = 0;
                    }
                    $stats[$storeName]++;
                }
            }

        }

        arsort($stats);

        $top5Stores = array_slice($stats, 0, 10, true);

        return $top5Stores;
    }

    private function getTopGain($tickets)
    {
        $stats = [];
        $userIds = [];
        foreach ($tickets as $ticket) {
            $user = $ticket->getUser();
            if ($user && !in_array($user->getId(), $userIds)) {
                $userIds[] = $user->getId();
                $username = $user->getFirstName() . ' ' . $user->getLastName();
                $stats[$username] = [
                    "username" => $username,
                    "tickets" => 0,
                    "gains" => 0,
                    "level" => 0,
                ];
            }
        }

        foreach ($tickets as $ticket) {
            $user = $ticket->getUser();
            if ($user) {
                $username = $user->getFirstName() . ' ' . $user->getLastName();
                $stats[$username]['tickets']++;
                if ($ticket->getStatus() == Ticket::STATUS_WINNER) {
                    $stats[$username]['gains'] += 1;
                    $calCulResult = $ticket->getPrize()->getPrice() * 0.5;
                    $calCulResult2Digits = number_format($calCulResult, 2, ',', ' ');
                    $stats[$username]['level'] += floatval($calCulResult2Digits);
                }
            }
        }


        usort($stats, function ($a, $b) {
            $levelA = $a['level'];
            $levelB = $b['level'];
            return $levelB - $levelA;
        });

        $i = 0;
        foreach ($stats as $username => $stat) {
            $i++;
            $stats[$username]['key'] = $i;
        }


        $top5Clients = array_slice($stats, 0, 50, true);

        return $top5Clients;
    }

    private function getParticipationTendance($tickets)
    {
        $stats = [];
        $statsAux = [];
        $thirdStats = [];
        $cancelledStats = [];
        $expiredStats = [];

        foreach ($tickets as $ticket) {
            $ticketHistory = $ticket->getTicketHistories();
            foreach ($ticketHistory as $history) {
                $date = $history->getUpdatedAt()->format('d/m/Y');

                if (!isset($stats[$date])) {
                    $stats[$date] = 0;
                }
                if ($history->getStatus() == Ticket::STATUS_PRINTED) {
                    $stats[$date]++;
                }

                if (!isset($statsAux[$date])) {
                    $statsAux[$date] = 0;
                }
                if ($history->getStatus() == Ticket::STATUS_PENDING_VERIFICATION) {
                    $statsAux[$date]++;
                }

                if (!isset($thirdStats[$date])) {
                    $thirdStats[$date] = 0;
                }
                if ($history->getStatus() == Ticket::STATUS_WINNER) {
                    $thirdStats[$date]++;
                }

                if (!isset($cancelledStats[$date])) {
                    $cancelledStats[$date] = 0;
                }
                if ($history->getStatus() == Ticket::STATUS_CANCELLED) {
                    $cancelledStats[$date]++;
                }

                if (!isset($expiredStats[$date])) {
                    $expiredStats[$date] = 0;
                }
                if ($history->getStatus() == Ticket::STATUS_EXPIRED) {
                    $expiredStats[$date]++;
                }

            }
        }


        uksort($stats, function ($a, $b) {
            $dateA = DateTime::createFromFormat('d/m/Y', $a);
            $dateB = DateTime::createFromFormat('d/m/Y', $b);

            return $dateA <=> $dateB;
        });

        uksort($statsAux, function ($a, $b) {
            $dateA = DateTime::createFromFormat('d/m/Y', $a);
            $dateB = DateTime::createFromFormat('d/m/Y', $b);

            return $dateA <=> $dateB;
        });

        uksort($thirdStats, function ($a, $b) {
            $dateA = DateTime::createFromFormat('d/m/Y', $a);
            $dateB = DateTime::createFromFormat('d/m/Y', $b);

            return $dateA <=> $dateB;
        });

        uksort($cancelledStats, function ($a, $b) {
            $dateA = DateTime::createFromFormat('d/m/Y', $a);
            $dateB = DateTime::createFromFormat('d/m/Y', $b);

            return $dateA <=> $dateB;
        });

        uksort($expiredStats, function ($a, $b) {
            $dateA = DateTime::createFromFormat('d/m/Y', $a);
            $dateB = DateTime::createFromFormat('d/m/Y', $b);

            return $dateA <=> $dateB;
        });


        $stats = array_reverse($stats);
        $statsAux = array_reverse($statsAux);
        $thirdStats = array_reverse($thirdStats);
        $cancelledStats = array_reverse($cancelledStats);
        $expiredStats = array_reverse($expiredStats);


        return [
            "stats" => $stats,
            "statsAux" => $statsAux,
            "thirdStats" => $thirdStats,
            "cancelledStats" => $cancelledStats,
            "expiredStats" => $expiredStats,
        ];
    }

    private function getStatsByStatuses($tickets)
    {
        $statuses = ["printed", "pending", "confirmed", "cancelled", "expired"];
        $stats = array_fill_keys($statuses, 0);

        foreach ($tickets as $ticket) {
            switch ($ticket->getStatus()) {
                case Ticket::STATUS_PRINTED:
                    $stats["printed"]++;
                    break;
                case Ticket::STATUS_PENDING_VERIFICATION:
                    $stats["pending"]++;
                    break;
                case Ticket::STATUS_WINNER:
                    $stats["confirmed"]++;
                    break;
                case Ticket::STATUS_CANCELLED:
                    $stats["cancelled"]++;
                    break;
                case Ticket::STATUS_EXPIRED:
                    $stats["expired"]++;
                    break;
            }
        }


        $resArray = [];
        $resArray[] = [
            "name" => "Imprimés",
            "value" => $stats["printed"],
        ];
        $resArray[] = [
            "name" => "En attente",
            "value" => $stats["pending"],
        ];
        $resArray[] = [
            "name" => "Confirmés",
            "value" => $stats["confirmed"],
        ];
        $resArray[] = [
            "name" => "Annulés",
            "value" => $stats["cancelled"],
        ];
        $resArray[] = [
            "name" => "Expirés",
            "value" => $stats["expired"],
        ];


        return $resArray;
    }

    private function getGainByGenderByAge($tickets)
    {
        $stats = [];
        $userIds = [];
        foreach ($tickets as $ticket) {
            $user = $ticket->getUser();
            if ($user && !in_array($user->getId(), $userIds)) {
                $userIds[] = $user->getId();
                $age = $user->getAge();
                $gender = strtolower($user->getGender());
                if ($age >= 18 && $age <= 24) {
                    if (!isset($stats[0])) {
                        $stats[0] = [
                            "homme" => 0,
                            "femme" => 0,
                        ];
                    }
                    $stats[0][$gender]++;
                }
                if ($age >= 25 && $age <= 34) {
                    if (!isset($stats[1])) {
                        $stats[1] = [
                            "homme" => 0,
                            "femme" => 0,
                        ];
                    }
                    $stats[1][$gender]++;
                }
                if ($age >= 35 && $age <= 44) {
                    if (!isset($stats[2])) {
                        $stats[2] = [
                            "homme" => 0,
                            "femme" => 0,
                        ];
                    }
                    $stats[2][$gender]++;
                }
                if ($age >= 45 && $age <= 54) {
                    if (!isset($stats[3])) {
                        $stats[3] = [
                            "homme" => 0,
                            "femme" => 0,
                        ];
                    }
                    $stats[3][$gender]++;
                }

                if ($age >= 55) {
                    if (!isset($stats[4])) {
                        $stats[4] = [
                            "homme" => 0,
                            "femme" => 0,
                        ];
                    }
                    $stats[4][$gender]++;
                }

            }
        }

        return $stats;
    }

    private function getPlayGameTendance($tickets)
    {
        $stats = [];
        $statsAux = [];
        $thirdStats = [];
        $cancelledStats = [];
        $expiredStats = [];

        foreach ($tickets as $ticket) {
            $ticketHistory = $ticket->getTicketHistories();
            foreach ($ticketHistory as $history) {
                $date = $history->getUpdatedAt()->format('d/m/Y');

                if (!isset($stats[$date])) {
                    $stats[$date] = 0;
                }
                if ($history->getStatus() == Ticket::STATUS_PRINTED) {
                    $stats[$date]++;
                }

                if (!isset($statsAux[$date])) {
                    $statsAux[$date] = 0;
                }
                if ($history->getStatus() == Ticket::STATUS_PENDING_VERIFICATION) {
                    $statsAux[$date]++;
                }

                if (!isset($thirdStats[$date])) {
                    $thirdStats[$date] = 0;
                }
                if ($history->getStatus() == Ticket::STATUS_WINNER) {
                    $thirdStats[$date]++;
                }


            }
        }

        uksort($stats, function ($a, $b) {
            $dateA = DateTime::createFromFormat('d/m/Y', $a);
            $dateB = DateTime::createFromFormat('d/m/Y', $b);

            return $dateA <=> $dateB;
        });

        uksort($statsAux, function ($a, $b) {
            $dateA = DateTime::createFromFormat('d/m/Y', $a);
            $dateB = DateTime::createFromFormat('d/m/Y', $b);

            return $dateA <=> $dateB;
        });

        uksort($thirdStats, function ($a, $b) {
            $dateA = DateTime::createFromFormat('d/m/Y', $a);
            $dateB = DateTime::createFromFormat('d/m/Y', $b);

            return $dateA <=> $dateB;
        });


        $stats = array_reverse($stats);
        $statsAux = array_reverse($statsAux);
        $thirdStats = array_reverse($thirdStats);


        return [
            "stats" => $stats,
            "statsAux" => $statsAux,
            "thirdStats" => $thirdStats,
        ];
    }

    private function getTotalPrizesPrice($tickets)
    {
        $totalPrizesPrice = 0;
        foreach ($tickets as $ticket) {
            if ($ticket->getStatus() == Ticket::STATUS_WINNER) {
                $totalPrizesPrice += $ticket->getPrize()->getPrice();
            }
        }

        $totalPrizesPrice = number_format($totalPrizesPrice, 2, ',', ' ');

        return $totalPrizesPrice;
    }

    private function getPrizesCostTendance($tickets)
    {
        $stats = [];
        $statsAux = [];

        foreach ($tickets as $ticket) {
            $ticketHistory = $ticket->getTicketHistories();
            foreach ($ticketHistory as $history) {
                $date = $history->getUpdatedAt()->format('d/m/Y');

                if (!isset($stats[$date])) {
                    $stats[$date] = 0;
                }
                if ($history->getStatus() == Ticket::STATUS_PENDING_VERIFICATION) {
                    $stats[$date] += $ticket->getPrize()->getPrice();
                }

                if (!isset($statsAux[$date])) {
                    $statsAux[$date] = 0;
                }
                if ($history->getStatus() == Ticket::STATUS_WINNER) {
                    $statsAux[$date] += $ticket->getPrize()->getPrice();
                }
            }
        }

        uksort($stats, function ($a, $b) {
            $dateA = DateTime::createFromFormat('d/m/Y', $a);
            $dateB = DateTime::createFromFormat('d/m/Y', $b);

            return $dateA <=> $dateB;
        });

        uksort($statsAux, function ($a, $b) {
            $dateA = DateTime::createFromFormat('d/m/Y', $a);
            $dateB = DateTime::createFromFormat('d/m/Y', $b);

            return $dateA <=> $dateB;
        });

        $stats = array_reverse($stats);
        $statsAux = array_reverse($statsAux);

        return [
            "stats" => $stats,
            "statsAux" => $statsAux,
        ];


    }





}
