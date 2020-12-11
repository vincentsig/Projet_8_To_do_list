<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{
    protected function supports($attribute, $task)
    {

        return in_array($attribute, ['TASK_DELETE'])
            && $task instanceof \App\Entity\Task;
    }

    protected function voteOnAttribute($attribute, $task, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'TASK_DELETE':
                if ($task->getAuthor() === null && in_array('ROLE_ADMIN', $user->getRoles(), true)) {
                    return true;
                }
                return $task->getAuthor() === $user;
                break;
        }

        return false;
    }
}
