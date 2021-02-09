<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Tests\UserFactory;
use DateTime;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    use UserFactory;

    /**
     * @test
     */
    public function test_updatedAt()
    {
        $user = new User();
        $date = new DateTime('now');

        $user->setUpdatedAt($date);
        $updatatedDate = $user->getUpdatedAt();

        $this->assertSame($date, $updatatedDate);
    }
}
