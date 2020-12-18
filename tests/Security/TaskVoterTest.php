<?php

namespace App\Test\Security;

use App\Entity\Task;
use App\Entity\User;
use ReflectionClass;
use PHPUnit\Framework\TestCase;
use App\Security\Voter\TaskVoter;
use ReflectionMethod;
use ReflectionException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TaskVoterTest extends TestCase
{
    /**
     * Get protected or private method on an object
     * @param mixed $obj
     * @param mixed $name
     * @return ReflectionMethod
     * @throws ReflectionException
     */
    private function getPrivateMethod($obj, $name)
    {
        $class = new ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * @test
     */
    public function user_can_acces_to_delete_task()
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);

        $task = new Task();
        $task->setAuthor(null);
        $voter = new TaskVoter();
        $method = $this->getPrivateMethod($voter, 'voteOnAttribute');
        $tokenMock = $this->CreateMock(TokenInterface::class);
        $tokenMock->method('getUser')->willReturn($user);

        $this->assertTrue($method->invokeArgs($voter, ['delete', $task, $tokenMock]));
    }

    /**
     * @test
     */
    public function anonymous_can__not_acces_to_delete_task()
    {
        $user = null;

        $task = new Task();
        $task->setAuthor(null);
        $voter = new TaskVoter();
        $method = $this->getPrivateMethod($voter, 'voteOnAttribute');
        $tokenMock = $this->CreateMock(TokenInterface::class);
        $tokenMock->method('getUser')->willReturn($user);

        $this->assertFalse($method->invokeArgs($voter, ['delete', $task, $tokenMock]));
    }

    /**
     * @test
     */
    public function user_can_delete_his_own_task()
    {
        $user = new User();

        $task = new Task();
        $task->setAuthor($user);
        $voter = new TaskVoter();
        $method = $this->getPrivateMethod($voter, 'voteOnAttribute');
        $tokenMock = $this->CreateMock(TokenInterface::class);
        $tokenMock->method('getUser')->willReturn($user);

        $this->assertTrue($method->invokeArgs($voter, ['delete', $task, $tokenMock]));
    }

    /**
     * @test
     */
    public function user_can_not_delete_if_they_are_not_the_author_of_the_task()
    {
        $user1 = new User();
        $user2 = new User();

        $task = new Task();
        $task->setAuthor($user1);
        $voter = new TaskVoter();
        $method = $this->getPrivateMethod($voter, 'voteOnAttribute');
        $tokenMock = $this->CreateMock(TokenInterface::class);
        $tokenMock->method('getUser')->willReturn($user2);

        $this->assertFalse($method->invokeArgs($voter, ['delete', $task, $tokenMock]));
    }

    /**
     * @test
     */
    public function user_can_not_delete_anonymous_task()
    {
        $user = new User();

        $task = new Task();
        $voter = new TaskVoter();
        $method = $this->getPrivateMethod($voter, 'voteOnAttribute');
        $tokenMock = $this->CreateMock(TokenInterface::class);
        $tokenMock->method('getUser')->willReturn($user);

        $this->assertFalse($method->invokeArgs($voter, ['delete', $task, $tokenMock]));
    }

    /**
     * @test
     */
    public function admin_can_delete_anonymous_task()
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);

        $task = new Task();
        $task->setAuthor(null);

        $voter = new TaskVoter();
        $method = $this->getPrivateMethod($voter, 'voteOnAttribute');
        $tokenMock = $this->CreateMock(TokenInterface::class);
        $tokenMock->method('getUser')->willReturn($user);

        $this->assertTrue($method->invokeArgs($voter, ['delete', $task, $tokenMock]));
    }

    /**
     * @test
     */
    public function invalid_attributes_should_return_a_logicException()
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);

        $task = new Task();
        $task->setAuthor($user);

        $voter = new TaskVoter();
        $method = $this->getPrivateMethod($voter, 'voteOnAttribute');
        $tokenMock = $this->CreateMock(TokenInterface::class);
        $tokenMock->method('getUser')->willReturn($user);
        $this->expectException(\LogicException::class);
        $method->invokeArgs($voter, ['invalid_attribute', $task, $tokenMock]);
    }
}
