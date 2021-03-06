<?php

namespace App\Tests\Controller;

use DateTime;
use App\Entity\Task;
use App\Entity\User;
use App\Tests\UserFactory;
use App\Tests\Framework\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    use UserFactory;

    /**
     * Generate Task By default if no data is set,
     * if some data are set the task will be overrrides with array_merge.
     *
     * @param array $overrides
     */
    private function createTask($overrides = [], User $user = null): Task
    {
        $data = array_merge([
            'createdAt' => new DateTime('+ 1 days'),
            'title' => 'task test',
            'content' => 'this is a content test',
            'author' => null,
            'isDone' => false
        ], $overrides);

        $task = (new Task($data))
            ->setCreatedAt($data['createdAt'])
            ->setTitle($data['title'])
            ->setContent($data['content'])
            ->setAuthor($user);

        $task->toggle($data['isDone']);

        $this->em->persist($task);
        $this->em->flush();

        return $task;
    }

    /**
     * @test
     */
    public function task_list_should_list_the_right_number_of_tasks()
    {
        $this->getAdminLogin();
        for ($i = 1; $i <= 8; ++$i) {
            $this->createTask();
        }

        $this->visit('/tasks')
        ->assertResponseOK()
        ->assertCount(8, $this->crawler->filter('.card'));
    }

    /**
     * @test
     */
    public function if_tasks_list_has_no_task_yet_it_should_display_message_no_task_saved()
    {
        $this->getAdminLogin();
        $this->visit('/tasks');

        $this->seeText("Il n'y a pas encore de tâche enregistrée.");
    }

    /**
     * @test
     */
    public function show_should_return_404_response_if_task_id_do_not_exist()
    {
        $this->getAdminLogin();
        for ($i = 1; $i < 5; ++$i) {
            $this->createTask();
        }

        $this->visit('/tasks/8/edit');
        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * @test
     */
    public function navigation_from_list_task_to_task_edit_should_work()
    {
        $this->getAdminLogin();
        $task1 = $this->createTask(['title' => 'task_1']);
        $task2 = $this->createTask(['title' => 'task_2']);

        $this->visit('/tasks')
        ->clickLink($task1->getTitle())
        ->seePageIs('/tasks/' . $task1->getId() . '/edit');

        $this->visit('/tasks')
        ->clickLink($task2->getTitle())
        ->seePageIs('/tasks/' . $task2->getId() . '/edit');
    }

    /**
     * @test
     */
    public function edit_task_page_should_display_task_form()
    {
        $this->getAdminLogin();
        $task = $this->createTask(['title' => 'title']);

        $this->visit('/tasks/' . $task->getId() . '/edit');
        $this->assertSelectorExists('form');
        $this->assertSelectorExists("input[name='task[title]']");
        $this->assertSelectorExists("textarea[name='task[content]']");
    }

    /**
     * @test
     */
    public function edit_and_saving_task_should_display_flash_message_and_redirect_task_list()
    {
        $this->getAdminLogin();
        $task = $this->createTask(['title' => 'title']);

        $this->visit('/tasks/' . $task->getId() . '/edit');

        $form = $this->crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'title edit test';
        $form['task[content]'] = 'content edit test';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->seePageIs('/tasks');
        $this->assertElementTextContains('Superbe ! La tâche a bien été modifiée.', $crawler->filter('div.alert'));
    }

    /**
     * @test
     */
    public function navigation_from_list_task_to_create_task_should_work()
    {
        $this->getAdminLogin();
        $this->visit('/tasks')
            ->assertResponseOK()
            ->clickLink('Créer une tâche')
            ->seePageIs('/tasks/create');
    }

    /**
     * @test
     */
    public function click_on_delete_button_should_remove_the_task()
    {
        $this->getAdminLogin();
        $task2 = $this->createTask(['title' => 'task_2']);

        $this->visit('/tasks');
        $this->SeeText($task2->getTitle());

        $this->client->followRedirects(true);
        $this->visit('/tasks/' . $task2->getId() . '/delete', 'DELETE');

        $this->seePageIs('/tasks');
        $this->dontSeeText($task2->getTitle());
        $this->assertElementTextContains(
            'Superbe ! La tâche a bien été supprimée.',
            $this->crawler->filter('div.alert')
        );
    }


    /**
     * @test
     */
    public function user_can_not_remove_a_task_if_they_are_not_the_author()
    {
        $this->getAdminLogin();

        $user1 = $this->createUser([
            'username' => 'user1',
            'email' => 'randomemail@gmail.com',
        ]);
        $task1 = $this->createTask([
            'title' => 'task_1'
        ], $user1);

        $this->client->followRedirects(true);
        $this->visit('/tasks/' . $task1->getId() . '/delete', 'DELETE');
        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * @test
     */
    public function user_with_admin_role_can_not_remove_anonymous_user()
    {
        $user = $this->createUser(['roles' => ['ROLE_USER']]);
        $this->client->loginUser($user);
        $task1 = $this->createTask([
            'title' => 'task_1',
            $user
        ]);
        $this->client->followRedirects(true);
        $this->visit('/tasks/' . $task1->getId() . '/delete', 'DELETE');
        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * @test
     */
    public function user_with_admin_role_can_remove_anonymous_user()
    {
        $this->getAdminLogin();
        $task1 = $this->createTask([
            'title' => 'task_1',
            'author' => null,
        ]);
        $this->client->followRedirects(true);
        $this->visit('/tasks/' . $task1->getId() . '/delete', 'DELETE');
        $this->seePageIs('/tasks');
        $this->dontSeeText($task1->getTitle());
        $this->assertElementTextContains(
            'Superbe ! La tâche a bien été supprimée.',
            $this->crawler->filter('div.alert')
        );
    }

    /**
     * @test
     */
    public function create_task_should_display_task_form()
    {
        $this->getAdminLogin();
        $this->visit('/tasks/create');

        $this->assertSelectorExists('form');
        $this->assertSelectorExists("input[name='task[title]']");
        $this->assertSelectorExists("textarea[name='task[content]']");
    }

    /**
     * @test
     */
    public function create_a_new_task_should_redirect_to_tasks_list_and_display_the_task_and_flash_message()
    {
        $this->getAdminLogin();
        $this->visit('/tasks/create');

        $form = $this->crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'title test';
        $form['task[content]'] = 'content test';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->seePageIs('/tasks');
        $this->assertElementTextContains(
            'Superbe ! La tâche a bien été ajoutée.',
            $crawler->filter('div.alert')
        );

        $this->assertElementTextContains('title test', $crawler->filter('.card-body > h4')->eq(1));
        $this->assertElementTextContains('content test', $crawler->filter('p'));
    }

    /**
     * @test
     */
    public function click_on_marked_as_done_should_hide_task_and_display_flash_message()
    {
        $this->getAdminLogin();
        $task1 = $this->createTask(['content' => 'this content should not appear on tasks list not done']);
        $task2 = $this->createTask(['content' => 'this content should appear on tasks list not done']);

        $this->visit('/tasks/' . $task1->getId() . '/toggle');
        $crawler = $this->client->followRedirect();

        $this->assertElementTextNotContains(
            'this content should not appear on tasks list not done',
            $crawler->filter('p')
        );
        $this->assertElementTextContains(
            'this content should appear on tasks list not done',
            $crawler->filter('p')
        );
        $this->assertElementTextContains(
            'Superbe ! La tâche ' . $task1->getTitle() . ' a bien été marquée comme faite.',
            $crawler->filter('div.alert')
        );
    }

    /**
     * @test
     */
    public function click_on_marked_unfinished_should_and_flash_message()
    {
        $this->getAdminLogin();
        $task1 = $this->createTask([
            'title' => 'task test',
            'isDone' => 'true'
            ]);

        $this->visit('/tasks/done');

        $this->seeText('task test');
        $this->assertSelectorExists('span.fas.fa-check');

        $this->visit('/tasks/' . $task1->getId() . '/toggle');
        $crawler = $this->client->followRedirect();
        $this->assertElementTextContains(
            'Superbe ! La tâche ' . $task1->getTitle() . ' a bien été marquée comme non terminé.',
            $crawler->filter('div.alert')
        );
    }
}
