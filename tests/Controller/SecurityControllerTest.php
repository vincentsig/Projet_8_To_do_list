<?php

namespace App\Tests\Controller;

use App\Tests\UserFactory;
use App\Tests\Framework\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    use UserFactory;

    /**
     * @test
     */
    public function login_page_should_render_the_login_form()
    {
        $this->visit('/login');

        $this->assertResponseOk();
        $this->assertSelectorExists('form');
        $this->assertSelectorExists("input[name='_username']");
        $this->assertSelectorExists("input[name='_password']");
    }

    /**
     * @test
     */
    public function a_user_with_bad_credential_should_be_redirected_to_login_page_and_display_a_flash_message()
    {
        $this->visit('/login');
        $form = $this->crawler->selectButton('Se connecter')->form();
        $form['_username'] = "usertest";
        $form['_password'] = "bad password";
        $this->client->submit($form);

        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('div.alert.alert-danger', 'Oops ! Invalid credentials!');
    }

    /**
     * @test
     */
    public function a_user_with_good_credential_should_be_able_to_log_in()
    {
        $this->createUser([
        'username' => 'usertest',
        'password' =>
            '$argon2id$v=19$m=65536,t=4,p=1$WjdlNFo0VXpjOXU0SGdOdA$Yd9XKsrRNKlV693gpZA5se0OxNCx8bA/YO9MoTqSiP8'
        ]);

        $this->visit('/login');

        $csrfToken = $this->client->getContainer()->get('security.csrf.token_manager')->getToken('login_form');

        $form = $this->crawler->selectButton('Se connecter')->form();
        $form['_username'] = "usertest";
        $form['_password'] = "12345";
        $form['csrf_token'] = $csrfToken;
        $this->client->submit($form);

        $this->assertResponseRedirects('/');
        $this->client->followRedirect();

        $this->seePageIs('/');
        $this->assertSelectorTextContains('div.alert.alert-success', 'Superbe ! Vous êtes maintenant connecté');
    }

    /**
     * @test
     */
    public function logout_should_redirect_to_login_page()
    {
        $this->getAdminLogin();
        $this->visit('/');
        $this->client->followRedirects();
        $this->ClickLink('Se déconnecter');

        $this->seePageIs('/');
        $this->seeText('Se connecter');
    }
}
