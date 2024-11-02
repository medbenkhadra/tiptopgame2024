<?php


namespace App\Tests\Unit\Entity;


use App\Entity\ConnectionHistory;
use App\Entity\Role;
use App\Entity\User;
use DateTime;
use Doctrine\DBAL\Types\Types;
use PHPUnit\Framework\TestCase;

class ConnectionHistoryTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $connectionHistory = new ConnectionHistory();

        $user = new User();
        $user->setLastname('test');
        $user->setFirstname('test');
        $user->setDateOfBirth(new DateTime('2024-04-01'));
        $user->setCreatedAt(new DateTime('2024-04-01 10:00:00'));
        $user->setUpdatedAt(new DateTime('2024-04-01 10:00:00'));
        $role = new Role();
        $role->setName('ROLE_CLIENT');
        $user->setRole($role);

        $loginTime = new DateTime('2024-04-01 10:00:00');
        $logoutTime = new DateTime('2024-04-01 12:00:00');

        $connectionHistory->setUser($user);
        $this->assertEquals($user, $connectionHistory->getUser());

        $connectionHistory->setLoginTime($loginTime);
        $this->assertEquals($loginTime, $connectionHistory->getLoginTime());

        $connectionHistory->setLogoutTime($logoutTime);
        $this->assertEquals($logoutTime, $connectionHistory->getLogoutTime());

        $connectionHistory->setIsActive(true);
        $this->assertTrue($connectionHistory->isIsActive());

        $connectionHistory->setDuration('2 hours');
        $this->assertEquals('2 hours', $connectionHistory->getDuration());
    }

    public function testGetConnectionHistoryJson(): void
    {
        $connectionHistory = new ConnectionHistory();
        $connectionHistory->setId(1);

        $user = new User();
        $user->setLastname('test');
        $user->setFirstname('test');
        $user->setDateOfBirth(new DateTime('2024-04-01'));
        $user->setCreatedAt(new DateTime('2024-04-01 10:00:00'));
        $user->setUpdatedAt(new DateTime('2024-04-01 10:00:00'));
        $role = new Role();
        $role->setName('ROLE_CLIENT');
        $user->setRole($role);


        $connectionHistory->setUser($user);

        $loginTime = new DateTime('2024-04-01 10:00:00');
        $connectionHistory->setLoginTime($loginTime);

        $logoutTime = new DateTime('2024-04-01 12:00:00');
        $connectionHistory->setLogoutTime($logoutTime);

        $connectionHistory->setIsActive(true);
        $connectionHistory->setDuration('2 hours');

        $expectedJson = [
            'id' => 1,
            'user' => $user->getUserJson(),
            'login_time' => ['date' => '01-04-2024', 'time' => '10:00'],
            'logout_time' => ['date' => '01-04-2024', 'time' => '12:00'],
            'is_active' => true,
            'duration' => '2 hours',
        ];

        $this->assertEquals($expectedJson, $connectionHistory->getConnectionHistoryJson());
    }

    public function testGetLogoutTimeJsonWithLogoutTime(): void
    {
        $connectionHistory = new ConnectionHistory();

        $logoutTime = new DateTime('2024-04-01 12:00:00');
        $connectionHistory->setLogoutTime($logoutTime);

        $expectedJson = [
            'date' => '01-04-2024',
            'time' => '12:00',
        ];

        $this->assertEquals($expectedJson, $connectionHistory->getLogoutTimeJson());
    }

    public function testGetLogoutTimeJsonWithoutLogoutTime(): void
    {
        $connectionHistory = new ConnectionHistory();

        $this->assertEquals([], $connectionHistory->getLogoutTimeJson());
    }
}

