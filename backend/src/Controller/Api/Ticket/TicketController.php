<?php

namespace App\Controller\Api\Ticket;

use App\Entity\Badge;
use App\Entity\ClientFinalDraw;
use App\Entity\EmailService;
use App\Entity\LoyaltyPoints;
use App\Entity\TicketHistory;
use App\Entity\User;
use App\Service\Mailer\PostManMailerService;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use App\Entity\Role;
use App\Entity\Store;
use App\Entity\Ticket;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;


class TicketController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    private PostManMailerService $postManMailerService;


    public function __construct(EntityManagerInterface $entityManager , PostManMailerService $postManMailerService)
    {
        $this->entityManager = $entityManager;
        $this->postManMailerService = $postManMailerService;

    }



    public function getTicketByCode(string $code): JsonResponse
    {


        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('t')
            ->from(Ticket::class, 't');

        if ($code != "" && $code != null) {
            $qb->andWhere('t.ticket_code = :ticket_code')
                ->setParameter('ticket_code', $code);
        }


        $results = $qb->getQuery()->getResult();


        $jsonTickets= [];
        foreach ($results as $ticket) {
            $jsonTickets[] =
                $ticket->getTicketJson();
        }


        return $this->json([
            'tickets' => $jsonTickets,
        ], 200);
    }
    public function getTickets(Request $request): JsonResponse
    {

        $ticket_code =  $request->get('ticket_code' , null);
        $status =  $request->get('status' , null);
        $store =  $request->get('store' , null);
        $employee =  $request->get('caissier' , null);
        $client =  $request->get('client' , null);
        $prize =  $request->get('prize' , null);

        $page = $request->get('page' , 1);
        $limit = $request->get('limit' , 9);


        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('t')
            ->from(Ticket::class, 't');


        if ($ticket_code != "" && $ticket_code != null) {
            $qb->andWhere('t.ticket_code LIKE :ticket_code')
                ->setParameter('ticket_code', '%' . $ticket_code . '%');
        }

        if ($status != "" && $status != null) {
            $qb->andWhere('t.status = :status')
                ->setParameter('status', $status);
        }

        if ($store != "" && $store != null) {
            $qb->andWhere('t.store = :store')
                ->setParameter('store', $store);
        }


        if ($employee != "" && $employee != null) {
            $qb->innerJoin('t.employee', 'e')
                ->andWhere('e.firstname LIKE :employee or e.lastname LIKE :employee')
                ->setParameter('employee', '%' . $employee . '%');
        }

        if ($client != "" && $client != null) {
            $qb->innerJoin('t.user', 'u')
                ->andWhere('u.firstname LIKE :employee or u.lastname LIKE :employee')
                ->setParameter('employee', '%' . $client . '%');
        }


        if ($prize != "" && $prize != null) {
            $qb->innerJoin('t.prize', 'p')
                ->andWhere('p.id = :prize')
                ->setParameter('prize', $prize);

        }

        $userRole = $this->getUser()->getRoles()[0];
        if ($userRole == Role::ROLE_EMPLOYEE) {
            $qb->andWhere('t.employee = :employee')
                ->setParameter('employee', $this->getUser());
        }

        if ($userRole == Role::ROLE_STOREMANAGER) {
            $qb->andWhere('t.store = :store')
                ->setParameter('store', $this->getUser()->getStores()[0]);
        }

        if ($userRole == Role::ROLE_CLIENT) {
            $qb->andWhere('t.user = :user')
                ->setParameter('user', $this->getUser());
        }


        $totalCount = count($qb->getQuery()->getResult());




        $currentPage = $page ?? 1;
        $pageSize = $limit ?? 9;
        $qb->setFirstResult(($currentPage - 1) * $pageSize)
            ->setMaxResults($pageSize);

        $results = $qb->getQuery()->getResult();


        $jsonTickets= [];
        foreach ($results as $ticket) {
            $jsonTickets[] =
                $ticket->getTicketJson();
        }



        return $this->json([
            'tickets' => $jsonTickets,
            'totalCount' => $totalCount

        ], 200);
    }


    public function getPendingTickets(Request $request): JsonResponse
    {

        $ticket_code =  $request->get('ticket_code' , null);
        $status =  $request->get('status' , null);
        $store =  $request->get('store' , null);
        $employee =  $request->get('caissier' , null);
        $client =  $request->get('client' , null);
        $prize =  $request->get('prize' , null);
        $keyword =  $request->get('keyword' , null);



        $page = $request->get('page' , 1);
        $limit = $request->get('limit' , 9);


        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('t')
            ->from(Ticket::class, 't');


        if ($ticket_code != "" && $ticket_code != null) {
            $qb->andWhere('t.ticket_code LIKE :ticket_code')
                ->setParameter('ticket_code', '%' . $ticket_code . '%');
        }

        if ($status != "" && $status != null) {
            $qb->andWhere('t.status = :status')
                ->setParameter('status', $status);
        }

        if ($store != "" && $store != null) {
            $qb->andWhere('t.store = :store')
                ->setParameter('store', $store);
        }


        if ($employee != "" && $employee != null) {
            $qb->innerJoin('t.employee', 'e')
                ->andWhere('e.firstname LIKE :employee or e.lastname LIKE :employee')
                ->setParameter('employee', '%' . $employee . '%');
        }

        if(($client != "" && $client != null) || ($keyword != "" && $keyword != null)){
            $qb->innerJoin('t.user', 'u');
        }

        if ($client != "" && $client != null) {
            $qb->andWhere('u.firstname LIKE :employee or u.lastname LIKE :employee')
                ->setParameter('employee', '%' . $client . '%');
        }


        if ($prize != "" && $prize != null) {
            $qb->innerJoin('t.prize', 'p')
                ->andWhere('p.id = :prize')
                ->setParameter('prize', $prize);

        }


        if ($keyword != "" && $keyword != null) {
            $qb->andWhere('u.firstname LIKE :keyword or u.lastname LIKE :keyword or u.email LIKE :keyword or u.phone LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%');
        }


        $totalCount = count($qb->getQuery()->getResult());




        $currentPage = $page ?? 1;
        $pageSize = $limit ?? 9;
        $qb->setFirstResult(($currentPage - 1) * $pageSize)
            ->setMaxResults($pageSize);

        $results = $qb->getQuery()->getResult();


        $jsonTickets= [];
        foreach ($results as $ticket) {
            $jsonTickets[] =
                $ticket->getTicketJson();
        }



        return $this->json([
            'tickets' => $jsonTickets,
            'totalCount' => $totalCount

        ], 200);
    }

    public function checkTicketForPlay(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $ticketCode = $data['ticketCode'] ?? $request->query->get('ticketCode');




        $ticket = $this->entityManager->getRepository(Ticket::class)->findOneBy(
            ['ticket_code' => $ticketCode ,
                'status' => Ticket::STATUS_PRINTED,
            ]
        );

        $finalStatus = [
            Ticket::STATUS_WINNER,
            Ticket::STATUS_PENDING_VERIFICATION,
        ];



        $ticketAux = $this->entityManager->getRepository(Ticket::class)->createQueryBuilder('t')
            ->where('t.ticket_code = :ticket_code')
            ->andWhere('t.status IN (:status)')
            ->setParameter('ticket_code', $ticketCode)
            ->setParameter('status', $finalStatus)
            ->getQuery()
            ->getResult();

            

        if (count($ticketAux) > 0) {
            return $this->json([
                'status' => "error",
                'message' => "Ticket already played",
            ], 404);
        }

        $prize = null;
        if ($ticket) {
            $prize = $ticket->getPrize();
        }



        if (!$ticket) {
            return $this->json([
                'status' => "error",
                'message' => "Ticket not found",
            ], 404);
        }


        return $this->json([
            'status' => "success",
            'prize' => $prize->getPrizeJson(),
            'ticket' => $ticket->getTicketJson(),
        ], 200);
    }


    public function printTicketByEmployee(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $ticketCode = $data['ticketCode'] ?? null;

        $user = $this->getUser();
        $userStore = $user->getStores()[0];
        $ticket = $this->entityManager->getRepository(Ticket::class)->createQueryBuilder('t')
            ->where('t.ticket_code = :ticket_code')
            ->setParameter('ticket_code', $ticketCode)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$ticket) {
            return $this->json([
                'status' => "error",
                'message' => "Ticket not found",
            ], 404);
        }


        $anonymousRole = $this->entityManager->getRepository(Role::class)->findOneBy(['name' => Role::ROLE_ANONYMOUS]);

        $anonymousUser = $this->entityManager->getRepository(User::class)->findOneBy(['role' => $anonymousRole]);


        $ticket->setTicketPrintedAt(new \DateTime());
        $ticket->setStatus(Ticket::STATUS_PRINTED);
        $ticket->setEmployee($user);
        $ticket->setStore($userStore);
        $ticket->setUpdatedAt(new \DateTime());


        $ticketHistory = new TicketHistory();
        $ticketHistory->setTicket($ticket);
        $ticketHistory->setEmployee($user);
        $ticketHistory->setUser($anonymousUser);
        $ticketHistory->setStatus(Ticket::STATUS_PRINTED);
        $ticketHistory->setUpdatedAt(new \DateTime());

        $this->entityManager->persist($ticketHistory);
        $this->entityManager->persist($ticket);
        $this->entityManager->flush();


        return $this->json([
            'ticket' => $ticket->getTicketJson(),
        ], 200);

    }

    /**
     * @IsGranted("ROLE_EMPLOYEE")
     * @param Request $request
     * @return JsonResponse
     * @throws NonUniqueResultException
     */

    public function printRandomTicket(Request $request): JsonResponse
    {
        $generatedTickets = $this->entityManager
            ->getRepository(Ticket::class)
            ->createQueryBuilder('t')
            ->where('t.status = :status')
            ->setParameter('status', Ticket::STATUS_GENERATED)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $loggedEmployee = $this->getUser();

        if (!$generatedTickets) {
            return $this->json([
                'status' => "error",
                'message' => "No ticket to print",
            ], 404);
        }

        $anonymousUser = $this->entityManager->getRepository(User::class)->findOneBy(['role' => $this->entityManager->getRepository(Role::class)->findOneBy(['name' => Role::ROLE_ANONYMOUS])]);


        $generatedTickets->setTicketPrintedAt(new \DateTime());
        $generatedTickets->setStatus(Ticket::STATUS_PRINTED);
        $generatedTickets->setEmployee($loggedEmployee);
        $generatedTickets->setStore($loggedEmployee->getStores()[0]);
        $generatedTickets->setUpdatedAt(new \DateTime());

        $ticketHistory = new TicketHistory();
        $ticketHistory->setTicket($generatedTickets);
        $ticketHistory->setEmployee($loggedEmployee);
        $ticketHistory->setUser($anonymousUser);
        $ticketHistory->setStatus(Ticket::STATUS_PRINTED);
        $ticketHistory->setUpdatedAt(new \DateTime());


        $this->entityManager->persist($ticketHistory);
        $this->entityManager->persist($generatedTickets);
        $this->entityManager->flush();




        return $this->json([
            'ticket' => $generatedTickets->getTicketJson(),
        ], 200);

    }


    public function confirmTicketPlay(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return $this->json([
                'status' => "error",
                'message' => "User not found",
            ], 404);
        }


        $data = json_decode($request->getContent(), true);
        $ticketCode = $data['ticketCode'] ?? null;


        $ticket = $this->entityManager->getRepository(Ticket::class)->findOneBy(
            ['ticket_code' => $ticketCode,
                'status' => Ticket::STATUS_PRINTED
            ]
        );

        if (!$ticket) {
            return $this->json([
                'status' => "error",
                'message' => "Ticket not found",
            ], 404);
        }

        $ticket->setUser($this->getUser());
        $ticket->setStatus(Ticket::STATUS_PENDING_VERIFICATION);
        $ticket->setUpdatedAt(new \DateTime());
        $ticket->setWinDate(new \DateTime());

        $ticketHistory = new TicketHistory();
        $ticketHistory->setTicket($ticket);
        $ticketHistory->setEmployee($ticket->getEmployee());
        $ticketHistory->setUser($this->getUser());
        $ticketHistory->setStatus(Ticket::STATUS_PENDING_VERIFICATION);
        $ticketHistory->setUpdatedAt(new \DateTime());


        $points = 0;
        $user = $ticket->getUser();
        $prize = $ticket->getPrize();

        if ($prize) {
            $points = (int) (floatval($prize->getPrice()) * 10);
        }

        $lastUserBadges= [];
        foreach ($user->getBadges() as $badge){
            $lastUserBadges[] = $badge->getId();
        }


        $loyaltyPoint = new LoyaltyPoints();
        $loyaltyPoint->setPoints($points);
        $loyaltyPoint->setCreatedAt(new \DateTime());
        $user->addLoyaltyPoint($loyaltyPoint);


        
        $loyaltyPointsSum = 0;
        $userLoyaltyPoints= $user->getLoyaltyPoints();

        foreach ($userLoyaltyPoints as $item){
            $loyaltyPointsSum += $item->getPoints();
        }

        $loyaltyPointsSum += $points;


        $badgeLevels = [
            200 => 1,
            400 => 2,
            600 => 3,
            800 => 4,
            1000 => 5,
        ];

        $badgesIds = [];
        foreach ($badgeLevels as $pointsRange => $badgeId) {
            if ($loyaltyPointsSum >= $pointsRange) {
                $badgesIds[] = $badgeId;
            }
        }

        $badges = [];

        foreach ($badgesIds as $id) {
            $badges[] = $this->entityManager->getRepository(Badge::class)->find($id);
        }

        foreach ($badges as $badge){
            $user->addBadge($badge);
        }


        $newUserBadges =  array_diff($badgesIds,$lastUserBadges);



        $gainedBadges= [];

        foreach ($newUserBadges as $badgeId){
            $bd = $this->entityManager->getRepository(Badge::class)->find($badgeId);
            $gainedBadges[] = $bd->getBadgeJson();
        }

        if(count($gainedBadges) > 0){
            $this->postManMailerService->sendEmailTemplate(EmailService::EMAILSERVICE_BADGE_AWARD , $user , ['badges' => $gainedBadges]);
        }


        $this->entityManager->persist($loyaltyPoint);
        $this->entityManager->persist($user);
        $this->entityManager->persist($ticketHistory);
        $this->entityManager->persist($ticket);

        $this->postManMailerService->sendEmailTemplate(EmailService::EMAILSERVICE_WHEEL_OF_FORTUNE_PARTICIPATION , $this->getUser() , ['ticket' => $ticket]);

        $this->entityManager->flush();

        return $this->json([
            'ticket' => $ticket->getTicketJson(),
            'gainedBadges' => $gainedBadges,
            'userLoyaltyPoints' => $loyaltyPointsSum,
        ], 200);

    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function confirmTicketGain(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $ticketId = $data['ticketId'] ?? null;


        $ticket = $this->entityManager->getRepository(Ticket::class)->findOneBy(
            ['id' => $ticketId,
                'status' => Ticket::STATUS_PENDING_VERIFICATION
            ]
        );

        if (!$ticket) {
            return $this->json([
                'status' => "error",
                'message' => "Ticket not found",
            ], 404);
        }

        $ticket->setStatus(Ticket::STATUS_WINNER);
        $ticket->setUpdatedAt(new \DateTime());

        $ticketHistory = new TicketHistory();
        $ticketHistory->setTicket($ticket);
        $ticketHistory->setEmployee($this->getUser());
        $ticketHistory->setUser($ticket->getUser());
        $ticketHistory->setStatus(Ticket::STATUS_WINNER);
        $ticketHistory->setUpdatedAt(new \DateTime());

        $this->postManMailerService->sendEmailTemplate(EmailService::EMAILSERVICE_WIN_DECLARATION_CLIENT , $ticket->getUser() , ['ticket' => $ticket]);



        $this->entityManager->persist($ticketHistory);


        $this->entityManager->persist($ticket);
        $this->entityManager->flush();

        return $this->json([
            'ticket' => $ticket->getTicketJson(),
        ], 200);

    }


    public function getWinnerTicketsHistory(Request $request): JsonResponse
    {

        $ticket_code = $request->get('ticket_code', null);
        $store = $request->get('store', null);
        $employee = $request->get('caissier', null);
        $client = $request->get('client', null);
        $prize = $request->get('prize', null);
        $employeeId = $request->get('employee', null);





        $page = $request->get('page', 1);
        $limit = $request->get('limit', 9);

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('t')
            ->from(Ticket::class, 't');


        $qb->andWhere('t.status = :status')
            ->setParameter('status', Ticket::STATUS_WINNER);



        $qb->orderBy('t.id', 'DESC');


        $totalCount = count($qb->getQuery()->getResult());

        $currentPage = $page ?? 1;
        $pageSize = $limit ?? 9;
        $qb->setFirstResult(($currentPage - 1) * $pageSize)
            ->setMaxResults($pageSize);

        $results = $qb->getQuery()->getResult();

        $jsonTickets = [];
        foreach ($results as $ticket) {
            $jsonTickets[] =
                $ticket->getTicketJson();
        }


        return $this->json([
            'gains' => $jsonTickets,
            'totalCount' => $totalCount

        ], 200);
    }
    public function getWinnerTickets(Request $request): JsonResponse
    {

        $ticket_code = $request->get('ticket_code', null);
        $store = $request->get('store', null);
        $employee = $request->get('caissier', null);
        $client = $request->get('client', null);
        $prize = $request->get('prize', null);
        $employeeId = $request->get('employee', null);

        $start_date = $request->get('start_date', null);
        $end_date = $request->get('end_date', null);



        $page = $request->get('page', 1);
        $limit = $request->get('limit', 9);

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('t')
            ->from(Ticket::class, 't');


        $qb->andWhere('t.status = :status')
            ->setParameter('status', Ticket::STATUS_WINNER);


        if ($ticket_code != "" && $ticket_code != null) {
            $qb->andWhere('t.ticket_code LIKE :ticket_code')
                ->setParameter('ticket_code', '%' . $ticket_code . '%');
        }

        if ($store != "" && $store != null) {
            $qb->andWhere('t.store = :store')
                ->setParameter('store', $store);
        }

        if(($employeeId != "" && $employeeId != null) || ($employee != "" && $employee != null)) {
            $qb->innerJoin('t.employee', 'e');
        }

        if ($employee != "" && $employee != null) {
            $qb
                ->andWhere('e.firstname LIKE :employee or e.lastname LIKE :employee')
                ->setParameter('employee', '%' . $employee . '%');
        }

        if(($client != "" && $client != null && !intval($client)) || ($client != "" && $client != null && intval($client)) ){
            $qb->innerJoin('t.user', 'u');
        }

        if ($client != "" && $client != null && !intval($client)) {
            $qb
                ->andWhere('u.firstname LIKE :employee or u.lastname LIKE :employee')
                ->setParameter('employee', '%' . $client . '%');
        }

        if ($client != "" && $client != null && intval($client)) {
            $qb
                ->andWhere('u.id = :id')
                ->setParameter('id', $client);
        }

        if ($prize != "" && $prize != null) {
            $qb->innerJoin('t.prize', 'p')
                ->andWhere('p.id = :prize')
                ->setParameter('prize', $prize);
        }

        if ($employeeId != "" && $employeeId != null) {
            $qb
                ->andWhere('e.id = :employeeId')
                ->setParameter('employeeId', $employeeId);
        }

        $userRole = $this->getUser()->getRoles()[0];
        if ($userRole == Role::ROLE_EMPLOYEE) {
            $qb->andWhere('t.employee = :employee')
                ->setParameter('employee', $this->getUser());
        }

        if ($userRole == Role::ROLE_STOREMANAGER) {
            $qb->andWhere('t.store = :store')
                ->setParameter('store', $this->getUser()->getStores()[0]);
        }

        if ($userRole == Role::ROLE_CLIENT) {
            $qb->andWhere('t.user = :user')
                ->setParameter('user', $this->getUser());
        }

        if ($start_date && $end_date) {
            $qb->andWhere('t.updated_at BETWEEN :start_date AND :end_date')
                ->setParameter('start_date', $start_date)
                ->setParameter('end_date', $end_date);
        } elseif ($start_date) {
            $qb->andWhere('t.updated_at >= :start_date')
                ->setParameter('start_date', $start_date);
        } elseif ($end_date) {
            $qb->andWhere('t.updated_at <= :end_date')
                ->setParameter('end_date', $end_date);
        }

        $qb->orderBy('t.updated_at', 'DESC');


        $totalCount = count($qb->getQuery()->getResult());

        $currentPage = $page ?? 1;
        $pageSize = $limit ?? 9;
        $qb->setFirstResult(($currentPage - 1) * $pageSize)
            ->setMaxResults($pageSize);

        $results = $qb->getQuery()->getResult();

        $jsonTickets = [];
        foreach ($results as $ticket) {
            $jsonTickets[] =
                $ticket->getTicketJson();
        }


        return $this->json([
            'gains' => $jsonTickets,
            'totalCount' => $totalCount

        ], 200);
    }


    public function getTicketsHistory(Request $request): JsonResponse
    {
        $userRole = $this->getUser()->getRoles()[0];

        $ticket_code = $request->get('ticket_code', null);
        $store = $request->get('store', null);
        $employee = $request->get('employee', null);
        $client = $request->get('client', null);
        $status = $request->get('status', null);

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


        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);


        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('th')
            ->from(TicketHistory::class, 'th');

        if(
            ($userRole == Role::ROLE_STOREMANAGER) ||
            ($ticket_code != "" && $ticket_code != null) || ($status != "" && $status != null) || ($store != "" && $store != null)){
            $qb->innerJoin('th.ticket', 'tk');
        }

        if ($ticket_code != "" && $ticket_code != null) {
            $qb
                ->andWhere('tk.ticket_code LIKE :ticket_code')
                ->setParameter('ticket_code', '%' . $ticket_code . '%');
        }

        if ($status != "" && $status != null) {
            $qb->andWhere('th.status = :status')
                ->setParameter('status', $status);
        }else {
            $qb->AndWhere('th.status != :status')
            ->setParameter('status', Ticket::STATUS_GENERATED);
        }

        if ($store != "" && $store != null) {
            $qb
                ->andWhere('tk.store = :store')
                ->setParameter('store', $store);
        }

        if ($employee != "" && $employee != null) {
            $qb->innerJoin('th.employee', 'e')
                ->andWhere('e.id = :employee')
                ->setParameter('employee', $employee );
        }

        if ($client != "" && $client != null && !intval($client)) {
            $qb->innerJoin('th.user', 'u')
                ->andWhere('u.id = :client')
                ->setParameter('client', $client );
        }

        if ($client != "" && $client != null && intval($client)) {
            $qb->innerJoin('th.user', 'u')
                ->andWhere('u.id = :id')
                ->setParameter('id', $client);
        }

        if ($start_date && $end_date) {
            $qb->andWhere('th.updated_at BETWEEN :start_date AND :end_date')
                ->setParameter('start_date', $start_date)
                ->setParameter('end_date', $end_date);
        } elseif ($start_date) {
            $qb->andWhere('th.updated_at >= :start_date')
                ->setParameter('start_date', $start_date);
        } elseif ($end_date) {
            $qb->andWhere('th.updated_at <= :end_date')
                ->setParameter('end_date', $end_date);
        }





        if ($userRole == Role::ROLE_EMPLOYEE) {
            $qb->andWhere('th.employee = :employee')
                ->setParameter('employee', $this->getUser());
        }

        if ($userRole == Role::ROLE_STOREMANAGER) {
            $qb->andWhere('tk.store = :store')
                ->setParameter('store', $this->getUser()->getStores()[0]);
        }

        if ($userRole == Role::ROLE_CLIENT) {
            $qb
                ->andWhere('tk.user = :user')
                ->setParameter('user', $this->getUser());
        }

        $qb->orderBy('th.updated_at', 'DESC');

        $totalCount = count($qb->getQuery()->getResult());

        $currentPage = $page ?? 1;

        $pageSize = $limit ?? 10;

        $qb->setFirstResult(($currentPage - 1) * $pageSize)
            ->setMaxResults($pageSize);

        $results = $qb->getQuery()->getResult();

        $ticketHistory = [];

        foreach ($results as $item) {
            $ticketHistory[] =
                $item->getTicketHistoryJson();
        }

        return $this->json([
            'ticketHistory' => $ticketHistory,
            'totalCount' => $totalCount

        ], 200);



    }

    public function testFinalDraw(Request $request): JsonResponse
    {
        $participants = $this->entityManager->getRepository(User::class)->createQueryBuilder('u')
            ->innerJoin('u.tickets', 't')
            ->getQuery()
            ->getResult();

        if(count($participants) < 1){
            return $this->json([
                'status' => "error",
                'message' => "Not enough participants",
            ], 404);
        }

        $winner = $participants[array_rand($participants)];


        return $this->json([
            'status' => "success",
            'message' => "Final draw",
            'winner' => $winner->getFullName(),
            'user' => $winner->getUserJson(),
        ], 200);
    }


    public function realFinalDraw(Request $request): JsonResponse
    {
        $participants = $this->entityManager->getRepository(User::class)->createQueryBuilder('u')
            ->innerJoin('u.tickets', 't')
            ->getQuery()
            ->getResult();

        if(count($participants) < 1){
            return $this->json([
                'status' => "error",
                'message' => "Not enough participants",
            ], 404);
        }

        $winner = $participants[array_rand($participants)];


        return $this->json([
            'status' => "success",
            'message' => "Final draw",
            'winner' => $winner->getFullName(),
            'user' => $winner->getUserJson(),
        ], 200);
    }

    public function finalDrawHistory(Request $request): JsonResponse
    {
        $clientFinalDraw = $this->entityManager->getRepository(ClientFinalDraw::class)->createQueryBuilder('cfd')
            ->orderBy('cfd.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if(!$clientFinalDraw){
            return $this->json([
                'status' => "error",
                'message' => "No final draw history",
            ], 404);
        }

        return $this->json([
            'status' => "success",
            'message' => "Final draw history",
            'clientFinalDraw' => $clientFinalDraw->getClientFinalDrawAsJson(),
        ], 200);




    }



}
