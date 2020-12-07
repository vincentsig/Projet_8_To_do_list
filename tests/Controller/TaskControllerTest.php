<?php

namespace App\Test\Controller;

use DateTime;
use App\Entity\Task;
use App\Tests\Framework\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    /**
     * Generate Task By default if no data is set,
     * if some data are set the task will be overrrides with array_merge.
     *
     * @param array $overrides
     */
    private function createTask($overrides = []): Task
    {
        $data = array_merge([
            'createdAt' => new DateTime('+ 1 days'),
            'title' => 'task test',
            'content' => 'this is a content test',
            'author' => null,
        ], $overrides);

        $task = (new Task($data))
            ->setCreatedAt($data['createdAt'])
            ->setTitle($data['title'])
            ->setContent($data['content'])
            ->setAuthor(null);

        $this->em->persist($task);
        $this->em->flush();

        return $task;
    }

    /**
     * @test
     */
    public function task_list_should_list_the_right_number_of_tasks()
    {
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
        $this->visit('/tasks');

        $this->seeText("Il n'y a pas encore de tâche enregistrée.");
    }

    /**
     * @test
     */
    public function show_should_return_404_response_if_task_id_do_not_exist()
    {
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
    public function edit_task_should_display_task_form()
    {
        $task = $this->createTask(['title' => 'title']);

        $this->visit('/tasks/' . $task->getId() . '/edit');
        $this->assertSelectorExists('form');
        $this->assertSelectorExists("input[name='task[title]']");
        $this->assertSelectorExists("textarea[name='task[content]']");
    }

    /**
     * @test
     */
    public function navigation_from_list_task_to_create_task_should_work()
    {
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
        $task2 = $this->createTask(['title' => 'task_2']);

        $this->visit('/tasks');
        $this->SeeText($task2->getTitle());
        $this->client->followRedirects(true);

        $this->visit('/tasks/' . $task2->getId() . '/delete');

        $this->dontSeeText($task2->getTitle());
        $this->assertStringContainsString(
            'Superbe ! La tâche a bien été supprimée.',
            $this->crawler->filter('div.alert')->text()
        );
    }

    /**
     * @test
     */
    public function create_task_should_display_task_form()
    {
        $this->visit('/tasks/create');
        $this->assertSelectorExists('form');
        $this->assertSelectorExists("input[name='task[title]']");
        $this->assertSelectorExists("textarea[name='task[content]']");
    }

    /**
     * @test
     */
    public function create_a_new_task_should_redirect_to_tasks_list_and_display_the_taskand_flash_message()
    {
        $this->visit('/tasks/create');

        $form = $this->crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'title test';
        $form['task[content]'] = 'content test';
        $this->client->submit($form);

        $this->client->followRedirects();

        $this->visit('/tasks/');

        $this->assertStringContainsString(
            'Superbe ! La tâche a bien été ajoutée.',
            $this->crawler->filter('div.alert')->text()
        );
        $this->seeText('title test');
        $this->seeText('content test');
    }

    /**
     * @test
     */
    public function click_on_marked_as_done_should_display_check_icone_and_flash_message()
    {
        $task1 = $this->createTask();

        $this->client->followRedirects();

        $this->visit('/tasks/' . $task1->getId() . '/toggle');

        $this->assertSelectorExists('span.fas.fa-check');
        $this->assertStringContainsString(
            'Superbe ! La tâche ' . $task1->getTitle() . ' a bien été marquée comme faite.',
            $this->crawler->filter('div.alert')->text()
        );
    }

    /**
     * @test
     */
    public function click_on_marked_unfinished_should_display_check_icone_and_flash_message()
    {
        $this->createTask();

        $this->client->followRedirects();
        $this->visit('/tasks/');

        $this->assertSelectorExists('span.fas.fa-times');
    }
}
