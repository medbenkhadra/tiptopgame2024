<?php


namespace App\Tests\Unit\Entity;


use App\Entity\Badge;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class BadgeTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $badge = new Badge();

        $badge->setName('Gold Badge');
        $this->assertEquals('Gold Badge', $badge->getName());

        $badge->setDescription('This badge represents excellence.');
        $this->assertEquals('This badge represents excellence.', $badge->getDescription());
    }

    public function testAddUser(): void
    {
        $badge = new Badge();
        $user = new User();

        $badge->addUser($user);

        $this->assertCount(1, $badge->getUsers());
        $this->assertTrue($badge->getUsers()->contains($user));
        $this->assertTrue($user->getBadges()->contains($badge));
    }

    public function testRemoveUser(): void
    {
        $badge = new Badge();
        $user = new User();

        $badge->addUser($user);
        $badge->removeUser($user);

        $this->assertCount(0, $badge->getUsers());
        $this->assertFalse($badge->getUsers()->contains($user));
        $this->assertFalse($user->getBadges()->contains($badge));
    }

    public function testGetBadgeJson(): void
    {
        $badge = new Badge();
        $badge->setId(1);
        $badge->setName('Gold Badge');
        $badge->setDescription('This badge represents excellence.');

        $expectedJson = [
            'id' => 1,
            'name' => 'Gold Badge',
            'description' => 'This badge represents excellence.',
        ];

        $this->assertEquals($expectedJson, $badge->getBadgeJson());
    }

    public function testSetId(): void
    {
        $badge = new Badge();
        $badge->setId(1);
        $this->assertEquals(1, $badge->getId());
    }

    public function testGetId(): void
    {
        $badge = new Badge();
        $badge->setId(1);
        $this->assertEquals(1, $badge->getId());
    }

}

