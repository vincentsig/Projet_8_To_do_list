<?php

namespace App\tests\EventListener;

use App\Entity\User;
use App\EventListener\UserPasswordEncoderListener;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserPasswordEncoderListenerTest extends TestCase
{
    /**
     * @test
     */
    public function a_password_can_not_be_encoded_if_the_plainpassword_is_null_on_create_user()
    {
        $user = new  User();
        $user->setPlainPassword('');

        $mockArgs = $this->getMockBuilder(LifecycleEventArgs::class)->disableOriginalConstructor()->getMock();
        $mockEncoder = $this->getMockBuilder(
            UserPasswordEncoderInterface::class
        )
            ->disableOriginalConstructor()
            ->getMock();
        $listener = new UserPasswordEncoderListener($mockEncoder);

        $this->assertNull($listener->prePersist($user, $mockArgs));
    }

    /**
     * @test
     */
    public function a_password_should_not_be_encoded_if_the_plainpassword_is_null_on_edit_user()
    {
        $user = new  User();
        $user->setPlainPassword('');

        $mockArgs = $this->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockEncoder = $this->getMockBuilder(UserPasswordEncoderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $listener = new UserPasswordEncoderListener($mockEncoder);

        $this->assertNull($listener->preUpdate($user, $mockArgs));
    }


    /**
     * @test
     */
    public function a_password_should_be_encoded_if_there_is_a_plainpassword_on_create_user()
    {
        $user = new  User();
        $user->setPlainPassword('plain_password');


        $mockArgs = $this->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockEncoder = $this->getMockBuilder(UserPasswordEncoderInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['encodePassword', 'isPasswordValid', 'needsRehash'])
            ->getMock();

        $mockEncoder->expects($this->once())
        ->method('encodePassword')
        ->willReturn('encoded_password');

        $listener = new UserPasswordEncoderListener($mockEncoder);

        $listener->prePersist($user, $mockArgs);
        $this->assertEquals('encoded_password', $user->getPassword());
    }

    /**
     * @test
     */
    public function a_password_should_be_encoded_if_there_is_a_plainpassword_on_edit_user()
    {
        $user = new  User();
        $user->setPlainPassword('plain_password');


        $mockArgs = $this->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockEncoder = $this->getMockBuilder(UserPasswordEncoderInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['encodePassword', 'isPasswordValid', 'needsRehash'])
            ->getMock();

        $mockEncoder->expects($this->once())
        ->method('encodePassword')
        ->willReturn('encoded_password');

        $listener = new UserPasswordEncoderListener($mockEncoder);

        $listener->preUpdate($user, $mockArgs);
        $this->assertEquals('encoded_password', $user->getPassword());
    }
}
