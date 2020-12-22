<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use DateTime;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    /**
     * @test
     */
    public function test_createdAt()
    {
        $task = new Task();
        $this->assertInstanceOf(DateTime::class, $task->getCreatedAt());
        $createdAt = new DateTime('2020-12-25');
        $task->setCreatedAt($createdAt);

        $this->assertEquals($createdAt, $task->getCreatedAt());
    }

    /**
     * @test
     */
    public function task_isDone_is_false_by_default()
    {
        $task = new task();
        $this->assertSame(false, $task->isDone());
    }

    /**
     * @test
     */
    public function test_toggle()
    {
        $task = new task();
        $task->toggle(true);
        $this->assertSame(true, $task->isDone());
    }
}
