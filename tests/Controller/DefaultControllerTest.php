<?php

namespace App\Tests\Controller;

use App\Tests\Framework\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function firstTestForCi()
    {
        $this->getAdminLogin();
        $this->visit('/');

        $this->assertResponseIsSuccessful();
    }
}
