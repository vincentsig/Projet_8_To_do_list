<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 100; ++$i) {
            $task = new Task();
            $task->setTitle('task '.$i);
            $task->setContent($faker->paragraph(3, true));
            // 80% chance to have null (anonymous author)
            $task->setAuthor($faker->boolean(80) ? $this->getReference('user-'.(mt_rand(0, 29))) : null);
            $manager->persist($task);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
