<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * UserPasswordEncoderListener
 * @var UserPasswordEncoderListener
 */
class UserPasswordEncoderListener
{
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function prePersist(User $user, LifecycleEventArgs $event): void
    {
        if (!$user->getPlainPassword()) {
            return;
        }
        $this->encodePassword($user);
    }

    public function preUpdate(User $user, LifecycleEventArgs $event): void
    {
        if (!$user->getPlainPassword()) {
            return;
        }
        $this->encodePassword($user);
    }

    private function encodePassword(User $user): void
    {
        $user->setPassword($this->encoder->encodePassword($user, ($user->getPlainPassword())));
    }
}
