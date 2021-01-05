<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserPasswordEncoderListener
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function prePersist(User $user, LifecycleEventArgs $event): void
    {
        $user->setPassword($this->encoder->encodePassword($user, ($user->getPlainPassword())));
    }

    public function preUpdate(User $user, LifecycleEventArgs $event): void
    {
        $user->setPassword($this->encoder->encodePassword($user, ($user->getPlainPassword())));
    }
}
