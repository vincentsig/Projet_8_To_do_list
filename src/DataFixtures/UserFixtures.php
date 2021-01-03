<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 30; ++$i) {
            $user = new User();
            $user->setUsername($faker->userName());
            $user->setPassword($this->encoder->encodePassword($user, '123456'));
            $user->setEmail($faker->email());
            $manager->persist($user);
            $this->addReference('user-' . $i, $user);
        }
        $user = new User();
        $user->setUsername('user_test');
        $user->setPassword($this->encoder->encodePassword($user, '123456'));
        $user->setEmail($faker->email());
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);

        $manager->flush();
    }
}
