<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    /**
     * @test
     */
    public function a_task_should_have_an_attribute_isDone_false_by_default()
    {
        $task = new Task();
        $this->assertFalse($task->isDone());
    }

    /**
     * @test
     */
    public function the_value_passed_to_the_toogle_method_should_be_equals_to_the_attribute_isDone()
    {
        $task = new Task();
        $task->toggle(true);
        $this->assertTrue($task->isDone());
    }
}
