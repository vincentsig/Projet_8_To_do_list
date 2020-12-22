<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use App\Tests\UserFactory;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    use UserFactory;

    /**
     * @test
     */
    public function add_task()
    {
        $user = new User();
        $task = new Task();
        $user->addTask($task);

        $this->assertContains($task, $user->getTasks());
    }

    /**
     * @test
     */
    public function remove_task()
    {
        $user = new User();  
        $task1 = new Task();
        $task2 = new Task();

        $user->addTask($task1);
        $user->addTask($task2);
        $this->assertContains($task2, $user->getTasks());

        $user->removeTask($task2);
        $this->assertNotContains($task2, $user->getTasks());
    }
}
