<?php

namespace App\Tests;

use App\Entity\Task;
use App\Entity\User;

trait UserFactory
{
    /**
     * Generate a default User with ROLE_USER if no data is set,
     * if some data are set the user will be overrrides with array_merge.
     *
     * @param array $overrides
     */
    private function createUser($overrides = [], Task $task = null): User
    {
        $data = array_merge([
            'username' => 'username_test',
            'roles' => ['ROLE_USER'],
            'password' => '12345',
            'email' => 'test@gmail.com',

        ], $overrides);

        $user = (new User($data))
            ->setUsername($data['username'])
            ->setRoles($data['roles'])
            ->setpassword($data['password'])
            ->setEmail($data['email']);

        if (!$task === null) {
            $user->addTask($task);
        }

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
