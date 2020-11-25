<?php 

namespace AppBundle\DataFixtures;

use Faker\Factory;
use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class AppFixtures extends Fixture implements ContainerAwareInterface{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }


    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        // create 20 tasks! Bam!
        for ($i = 0; $i < 100; $i++) {
            $task = new Task();
            $task->setTitle('task '.$i);
            $task->setContent($faker->paragraph($nbSentences = 3, $variableNbSentences = true));
            $manager->persist($task);
        }

        for ($i = 0; $i <30; $i++)
        {
            $user = new User();
            $user->setUsername($faker->userName);
            $encoder = $this->container->get('security.password_encoder');
         
            $user->setPassword($encoder->encodePassword($user, '12345'));
            $user->setEmail($faker->email());
            $manager->persist($user);
            
        }
        $user = new User();
            $user->setUsername('user_test');
            $encoder = $this->container->get('security.password_encoder');
            $user->setPassword($encoder->encodePassword($user, '12345'));
            $user->setEmail($faker->email());
            $manager->persist($user);

        $manager->flush();
    }
}