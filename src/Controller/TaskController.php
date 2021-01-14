<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Service\TaskHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{
    private $taskHandler;

    public function __construct(TaskHandler $taskHandler)
    {
        $this->taskHandler  =  $taskHandler;
    }

    /**
     * @Route("/tasks", name="app_task_list")
     *
     * @param  TaskRepository $repo
     * @return Response
     */
    public function listAction(TaskRepository $repo): Response
    {
        return $this->render('task/list.html.twig', ['tasks' => $repo->findBy(['isDone' => false])]);
    }

    /**
     * @Route("/tasks/done", name="app_task_list_done")
     *
     * @param TaskRepository $repo
     * @return Response
     */
    public function listActionDone(TaskRepository $repo): Response
    {
        return $this->render('task/list.html.twig', ['tasks' => $repo->findBy(["isDone" => true])]);
    }

    /**
     * @Route("/tasks/create", name="app_task_create")
     *
     * @param  Request $request
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        $task = new Task();

        if ($this->taskHandler->handle($request, $task)) {
            $this->taskHandler->createTask();
            $this->addFlash('success', 'La tâche a bien été ajoutée.');

            return $this->redirectToRoute('app_task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $this->taskHandler->getForm()->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="app_task_edit") : Response
     *
     * @param  Task $task
     * @param  Request $request
     * @return Response
     */
    public function editAction(Task $task, Request $request): Response
    {

        if ($this->taskHandler->handle($request, $task)) {
            $this->taskHandler->editTask();
            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('app_task_list');
        }

        return $this->render('task/edit.html.twig', [
        'form' => $this->taskHandler->getForm()->createView(),
        'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="app_task_toggle")
     *
     * @param  Task $task
     * @return Response
     */
    public function toggleTaskAction(Task $task): Response
    {
        $this->taskHandler->toggleTask($task);

        if ($task->isDone() === true) {
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

            return $this->redirectToRoute('app_task_list');
        }

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme non terminé.', $task->getTitle()));

        return $this->redirectToRoute('app_task_list_done');
    }

    /**
     * @Route("/tasks/{id}/delete", name="app_task_delete")
     *
     * @param  Task $task
     * @return Response
     */
    public function deleteTaskAction(Task $task): Response
    {
        $this->denyAccessUnlessGranted('delete', $task);

        $this->taskHandler->deleteTask($task);

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('app_task_list');
    }
}
