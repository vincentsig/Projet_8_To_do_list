<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 100; ++$i) {
            $task = new Task();
            $task->setTitle('task '.$i);
            $task->setContent($faker->paragraph(3, true));
            $manager->persist($task);
        }

        for ($i = 0; $i < 30; ++$i) {
            $user = new User();
            $user->setUsername($faker->userName());

            $user->setPassword($this->encoder->encodePassword($user, '12345'));
            $user->setEmail($faker->email());
            $manager->persist($user);
        }
        $user = new User();
        $user->setUsername('user_test');
       
        $user->setPassword($this->encoder->encodePassword($user, '12345'));
        $user->setEmail($faker->email());
        $manager->persist($user);

        $manager->flush();
    }
}
