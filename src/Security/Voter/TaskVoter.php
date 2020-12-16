<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{
    public const DELETE = 'delete';

    private const ATTRIBUTES = [
        self::DELETE,
    ];

    protected function supports($attribute, $task)
    {

        return in_array($attribute, self::ATTRIBUTES)
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
            case self::DELETE:
                return $this->canDelete($user, $task);
        }
    }

    public function canDelete($user, $task)
    {
        if ($task->getAuthor() === $user) {
            return true;
        }

        if ($task->getAuthor() === null && in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }
        return false;
    }
}
