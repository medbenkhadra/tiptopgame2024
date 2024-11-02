<?php


namespace App\Tests\Unit\Entity;


use App\Entity\Avatar;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class AvatarTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $avatar = new Avatar();

        $avatar->setFilename('avatar.jpg');
        $this->assertEquals('avatar.jpg', $avatar->getFilename());

        $avatar->setPath('/path/to/avatar');
        $this->assertEquals('/path/to/avatar', $avatar->getPath());

        $user = new User();
        $avatar->setUser($user);
        $this->assertEquals($user, $avatar->getUser());
    }

    public function testGetAvatarUrl(): void
    {
        $avatar = new Avatar();
        $avatar->setFilename('avatar.jpg');
        $avatar->setPath('/path/to/avatar');

        $this->assertEquals('/path/to/avatar/avatar.jpg', $avatar->getAvatarUrl());
    }

    public function testGetAvatarJson(): void
    {
        $user = new User();

        $avatar = new Avatar();
        $avatar->setId(1);
        $avatar->setFilename('avatar.jpg');
        $avatar->setPath('/path/to/avatar');
        $avatar->setUser($user);

        $expectedJson = [
            'id' => 1,
            'filename' => 'avatar.jpg',
            'path' => '/path/to/avatar',
            'user' => null,
            'avatarUrl' => '/path/to/avatar/avatar.jpg',
        ];

        $this->assertEquals($expectedJson, $avatar->getAvatarJson());
    }

    public function testSetId(): void
    {
        $avatar = new Avatar();
        $avatar->setId(1);
        $this->assertEquals(1, $avatar->getId());
    }

    public function testGetId(): void
    {
        $avatar = new Avatar();
        $avatar->setId(1);
        $this->assertEquals(1, $avatar->getId());
    }
}
