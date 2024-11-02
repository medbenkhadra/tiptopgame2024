<?php

namespace App\Tests\Unit\Entity;

use App\Entity\GameConfig;
use PHPUnit\Framework\TestCase;

class GameConfigTest extends TestCase
{
    public function testGetId(): void
    {
        $gameConfig = new GameConfig();
        $this->assertNull($gameConfig->getId());
    }

    public function testGetSetStartDate(): void
    {
        $gameConfig = new GameConfig();
        $startDate = '2024-04-01';
        $gameConfig->setStartDate($startDate);
        $this->assertEquals($startDate, $gameConfig->getStartDate());
    }

    public function testGetSetTime(): void
    {
        $gameConfig = new GameConfig();
        $time = '12:00:00';
        $gameConfig->setTime($time);
        $this->assertEquals($time, $gameConfig->getTime());
    }
}
