<?php

namespace App\Test\Security;

use App\Entity\Task;
use App\Entity\User;
use ReflectionClass;
use PHPUnit\Framework\TestCase;
use App\Security\Voter\TaskVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TaskVoterTest extends TestCase
{

    public static function getPrivateMethod($obj, $name)
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
        $method = self::getPrivateMethod($voter, 'voteOnAttribute');
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
        $method = self::getPrivateMethod($voter, 'voteOnAttribute');
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

        $this->assertTrue($voter->canDelete($user, $task));
    }

    /**
     * @test
     */
    public function user_can_not_delete_if_they_are_not_the_author_of_the_task()
    {
        $user = new User();
        $randomAuthor = new User();

        $task = new Task();
        $task->setAuthor($randomAuthor);

        $voter = new TaskVoter();

        $this->assertFalse($voter->canDelete($user, $task));
    }

        /**
     * @test
     */
    public function user_can_not_delete_anonymous_task()
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);

        $task = new Task();
        $task->setAuthor(null);

        $voter = new TaskVoter();

        $this->assertFalse($voter->canDelete($user, $task));
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

        $this->assertTrue($voter->canDelete($user, $task));
    }
}
