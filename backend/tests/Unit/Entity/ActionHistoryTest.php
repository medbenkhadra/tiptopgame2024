<?php


namespace App\Tests\Unit\Entity;

use App\Entity\ActionHistory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Store;
use PHPUnit\Framework\TestCase;

class ActionHistoryTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $actionHistory = new ActionHistory();

        $actionHistory->setActionType(ActionHistory::STORES_MANAGEMENT);
        $this->assertEquals(ActionHistory::STORES_MANAGEMENT, $actionHistory->getActionType());

        $actionHistory->setDetails('Created new store');
        $this->assertEquals('Created new store', $actionHistory->getDetails());

        $createdAt = new \DateTime();
        $actionHistory->setCreatedAt($createdAt);
        $this->assertEquals($createdAt, $actionHistory->getCreatedAt());

        $userDoneAction = new User();
        $actionHistory->setUserDoneAction($userDoneAction);
        $this->assertEquals($userDoneAction, $actionHistory->getUserDoneAction());

        $userActionRelatedTo = new User();
        $actionHistory->setUserActionRelatedTo($userActionRelatedTo);
        $this->assertEquals($userActionRelatedTo, $actionHistory->getUserActionRelatedTo());

        $store = new Store();
        $actionHistory->setStore($store);
        $this->assertEquals($store, $actionHistory->getStore());
    }

    public function testCreatedAtJson(): void
    {
        $createdAt = new \DateTime();
        $actionHistory = new ActionHistory();
        $actionHistory->setCreatedAt($createdAt);

        $expectedJson = [
            'date' => $createdAt->format('d-m-Y'),
            'time' => $createdAt->format('H:i'),
        ];

        $this->assertEquals($expectedJson, $actionHistory->getCreatedAtJson());
    }

    public function testActionHistoryJson(): void
    {
        $createdAt = new \DateTime();
        $userDoneAction = new User();
        $userDoneAction->setEmail('test@test.com');
        $userDoneAction->setFirstName('Test');
        $userDoneAction->setLastName('Test');
        $userDoneAction->setDateOfBirth(new \DateTime('1990-01-01'));
        $userDoneAction->setCreatedAt(new \DateTime());
        $userDoneAction->setUpdatedAt(new \DateTime());
        $role = new Role();
        $role->setName('ROLE_ADMIN');
        $userDoneAction->setRole($role);

        $userActionRelatedTo = new User();
        $userActionRelatedTo->setEmail('test1@test.com');
        $userActionRelatedTo->setFirstName('Test1');
        $userActionRelatedTo->setLastName('Test1');
        $userActionRelatedTo->setDateOfBirth(new \DateTime('1990-01-01'));
        $userActionRelatedTo->setCreatedAt(new \DateTime());
        $userActionRelatedTo->setUpdatedAt(new \DateTime());
        $role = new Role();
        $role->setName('ROLE_CLIENT');
        $userActionRelatedTo->setRole($role);


        $actionHistory = new ActionHistory();
        $actionHistory->setActionType(ActionHistory::STORES_MANAGEMENT);
        $actionHistory->setDetails('Created new store');
        $actionHistory->setCreatedAt($createdAt);
        $actionHistory->setUserDoneAction($userDoneAction);
        $actionHistory->setUserActionRelatedTo($userActionRelatedTo);

        $expectedJson = [
            'id' => null,
            'action_type' => ActionHistory::STORES_MANAGEMENT,
            'details' => 'Created new store',
            'created_at' => [
                'date' => $createdAt->format('d-m-Y'),
                'time' => $createdAt->format('H:i'),
            ],
            'user_done_action' => $userDoneAction->getUserJson(),
            'user_action_related_to' => $userActionRelatedTo->getUserJson(),
        ];

        $this->assertEquals($expectedJson, $actionHistory->getActionHistoryJson());
    }
}