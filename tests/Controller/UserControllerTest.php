<?php

namespace App\Tests\Controller;

use App\Tests\UserFactory;
use App\Tests\Framework\WebTestCase;

class UserControllerTest extends WebTestCase
{
    use UserFactory;

    /**
     * @test
     */
    public function visitor_should_not_be_able_to_acces_to_user_list()
    {
        $this->visit('/users');
        $this->seeStatusCode(401);
    }

    /**
     * @test
     */
    public function user_with_ROLE_USER_should_not_be_able_to_acces_to_any_user_root()
    {
        $user =  $this->createUser();

        $this->client->loginUser($user);

        $this->visit('/users');
        $this->seeStatusCode(403);

        $this->visit('/users/create');
        $this->seeStatusCode(403);

        $this->visit('/users/' . $user->getId() . '/edit/');
        $this->seeStatusCode(403);
    }

    /**
     * @test
     */
    public function user_with_ROLE_ADMIN_should_be_able_to_acces_to_any_user_root()
    {
        $user =  $this->createUser(['roles' => ['ROLE_ADMIN']]);
        $this->client->loginUser($user);

        $this->visit('/users');
        $this->assertResponseOK();

        $this->visit('/users/create');
        $this->assertResponseOK();

        $this->visit('/users/' . $user->getId() . '/edit');
        $this->assertResponseOK();
    }

    /**
     * @test
     */
    public function edit_user_should_display_a_header_with_the_name_of_the_user_and_render_a_form()
    {
        $user =  $this->createUser(['roles' => ['ROLE_ADMIN']]);
        $this->client->loginUser($user);

        $this->visit('/users/' . $user->getId() . '/edit');
        $this->assertSame('Modifier username_test', $this->crawler->filter('h1')->text());
        $this->assertSelectorExists('form');
        $this->assertSelectorExists("input[name='user[username]']");
        $this->assertSelectorExists("input[name='user[plainPassword][first]']");
        $this->assertSelectorExists("select[name='user[roles][]']");
    }


    /**
     * @test
     */
    public function create_new_users_should_be_displayed_in_users_list()
    {
        $user =  $this->createUser(['roles' => ['ROLE_ADMIN']]);
        $this->client->loginUser($user);
        for ($i = 1; $i <= 4; $i++) {
            $this->createUser([
                'username' => 'usertest_' . $i,
                'email' => 'email' . $i . '@gmail.com'
                ]);
        }

        $this->visit('/users');
        // we have 5 users 1 user to we created to acces to the page + 4 users crested in the for loop
        $this->assertCount(5, $this->crawler->filter('tbody tr'));
        $this->assertElementTextContains('username_test', $this->crawler->filter('tbody tr')->eq(0));
        $this->assertElementTextContains('usertest_1', $this->crawler->filter('tbody tr')->eq(1));
    }


    /**
     * @test
     */
    public function create_a_new_user_should_display_a_flash_message_and_redirect_to_user_list()
    {
        $user =  $this->createUser(['roles' => ['ROLE_ADMIN']]);
        $this->client->loginUser($user);

        $this->visit('/users/create');
        $form = $this->crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = "user_create_test";
        $form['user[plainPassword][first]'] = "123456";
        $form['user[plainPassword][second]'] = "123456";
        $form['user[email]'] = "email@test.com";
        $form['user[roles]'] = 'ROLE_USER';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->seePageIs('/users');
        $this->assertElementTextContains(
            "Superbe ! L'utilisateur a bien été ajouté.",
            $crawler->filter('div.alert')
        );
    }

    /**
     * @test
     */
    public function edit_user_should_display_flash_message_and_redirect_to_user_list()
    {
        $user =  $this->createUser(['roles' => ['ROLE_ADMIN']]);
        $userTest =  $this->createUser([
            'username' => 'original_username',
            'email' => 'origin@mail.com'
            ]);
        $this->client->loginUser($user);

        $this->visit('/users/' . $userTest->getId() . '/edit');
        $form = $this->crawler->selectButton('Modifier')->form();
        $form['user[username]'] = "new_name";
        $form['user[plainPassword][first]'] = "123456";
        $form['user[plainPassword][second]'] = "123456";
        $form['user[email]'] = "email@test.com";
        $form['user[roles]'] = 'ROLE_USER';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->seePageIs('/users');
        $this->assertElementTextContains(
            "Superbe ! L'utilisateur a bien été modifié",
            $crawler->filter('div.alert')
        );
    }
}
