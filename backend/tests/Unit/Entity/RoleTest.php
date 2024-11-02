<?php


namespace App\Tests\Unit\Entity;

use App\Entity\Role;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{
    public function testAddUser(): void
    {
        $role = new Role();
        $user = new User();

        $role->addUser($user);

        $this->assertCount(1, $role->getUsers());
        $this->assertTrue($role->getUsers()->contains($user));
        $this->assertSame($role, $user->getRole());
    }

    public function testRemoveUser(): void
    {
        $role = new Role();
        $user = new User();

        $role->addUser($user);
        $role->removeUser($user);

        $this->assertCount(0, $role->getUsers());
        $this->assertFalse($role->getUsers()->contains($user));
        $this->assertNull($user->getRole());
    }

    public function testGetId(): void
    {
        $role = new Role();
        $this->assertNull($role->getId());
    }

    public function testGetName(): void
    {
        $role = new Role();
        $this->assertNull($role->getName());
    }

    public function testSetName(): void
    {
        $role = new Role();
        $role->setName('name');
        $this->assertSame('name', $role->getName());
    }

    public function testGetLabel(): void
    {
        $role = new Role();
        $this->assertNull($role->getLabel());
    }

    public function testSetLabel(): void
    {
        $role = new Role();
        $role->setLabel('label');
        $this->assertSame('label', $role->getLabel());
    }

    public function testFormatToRole(): void
    {
        $roleName = 'admin';
        $formattedRole = Role::formatToRole($roleName);

        $this->assertEquals('ROLE_ADMIN', $formattedRole);

        $roleName = 'client';
        $formattedRole = Role::formatToRole($roleName);

        $this->assertEquals('ROLE_CLIENT', $formattedRole);

    }



}
