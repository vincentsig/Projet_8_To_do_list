<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TaskVoter extends Voter
{
    public const DELETE = 'delete';

    private const ATTRIBUTES = [
        self::DELETE,
    ];


    protected function supports(string $attribute, $task): bool
    {
        return in_array($attribute, self::ATTRIBUTES)
            && $task instanceof \App\Entity\Task;
    }

    protected function voteOnAttribute(string $attribute, $task, TokenInterface $token)
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

        throw new \LogicException('Invalid attribute: ' . $attribute);
    }

    /**
     * canDelete
     *
     * @param  User $user
     * @param  Task $task
     * @return bool
     */
    public function canDelete(User $user, Task $task): bool
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
