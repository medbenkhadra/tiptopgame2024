<?php

namespace App\Service\User;

use App\Entity\ActionHistory;
use App\Entity\ConnectionHistory;
use App\Entity\Store;
use App\Entity\User;

use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;


class UserService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }


    private function formatDuration(\DateInterval $duration): string
    {
        $seconds = $duration->s;
        $minutes = $duration->i;
        $hours = $duration->h;

        if ($hours > 0) {
            return sprintf('%02dh%02dm%02ds', $hours, $minutes, $seconds);
        } elseif ($minutes > 0) {
            return sprintf('%02dm%02ds', $minutes, $seconds);
        } else {
            return sprintf('%ds', $seconds);
        }
    }

    public function createConnectionHistory(User $user): void
    {

        $activeConnection = $this->entityManager->getRepository(ConnectionHistory::class)->findOneBy(['user' => $user, 'isActive' => true]);
        if (!$activeConnection) {
            $connectionHistory = new ConnectionHistory();
            $connectionHistory->setUser($user);
            $connectionHistory->setLoginTime(new \DateTime());
            $connectionHistory->setIsActive(true);
        }else{
            $activeConnection->setIsActive(false);
            $activeConnection->setLogoutTime(new \DateTime());

            $activeConnectionDuration = $activeConnection->getLoginTime()->diff($activeConnection->getLogoutTime());
            $activeConnectionDuration = $this->formatDuration($activeConnectionDuration);
            $activeConnection->setDuration($activeConnectionDuration);


            $connectionHistory = new ConnectionHistory();
            $connectionHistory->setUser($user);
            $connectionHistory->setLoginTime(new \DateTime());
            $connectionHistory->setIsActive(true);
        }



        $this->entityManager->persist($connectionHistory);
        $this->entityManager->flush();
    }





    public function createActionHistory(string $actionType, User|UserInterface $userDoneAction, ?User $userActionRelatedTo , Store $store , string $details): void
    {

        $actionHistory = new ActionHistory();
        $actionHistory->setDetails($details);
        $actionHistory->setActionType($actionType);
        $actionHistory->setUserDoneAction($userDoneAction);
        $actionHistory->setUserActionRelatedTo($userActionRelatedTo);
        $actionHistory->setStore($store);
        $actionHistory->setCreatedAt(new \DateTime());

        $this->entityManager->persist($actionHistory);
        $this->entityManager->flush();

    }


}
