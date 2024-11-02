<?php


namespace App\Tests\Unit\Entity;


use App\Entity\LoyaltyPoints;
use App\Entity\Role;
use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;

class LoyaltyPointsTest extends TestCase
{
    public function testGetId(): void
    {
        $loyaltyPoints = new LoyaltyPoints();
        $this->assertNull($loyaltyPoints->getId());
    }

    public function testGetSetPoints(): void
    {
        $loyaltyPoints = new LoyaltyPoints();
        $loyaltyPoints->setPoints(100);
        $this->assertEquals(100, $loyaltyPoints->getPoints());
    }

    public function testGetSetCreatedAt(): void
    {
        $loyaltyPoints = new LoyaltyPoints();
        $createdAt = new DateTime('2022-01-01');
        $loyaltyPoints->setCreatedAt($createdAt);
        $this->assertEquals($createdAt, $loyaltyPoints->getCreatedAt());
    }

    public function testGetSetUser(): void
    {
        $loyaltyPoints = new LoyaltyPoints();
        $user = new User();
        $user->setEmail('test@test.com');
        $user->setFirstName('Test');
        $user->setLastName('Test');
        $user->setDateOfBirth(new DateTime('1990-01-01'));
        $user->setCreatedAt(new DateTime('2022-01-01'));
        $user->setUpdatedAt(new DateTime('2022-01-01'));

        $role = new Role();
        $role->setName('ROLE_CLIENT');
        $user->setRole($role);


        $loyaltyPoints->setUser($user);
        $this->assertEquals($user, $loyaltyPoints->getUser());
    }

}