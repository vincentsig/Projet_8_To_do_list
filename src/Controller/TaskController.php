<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Service\FormHandler\TaskCreateHandler;
use App\Service\FormHandler\TaskEditHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @Route("/tasks", name="app_task_list")
     * @param  TaskRepository $repo
     * @return Response
     */
    public function listAction(TaskRepository $repo): Response
    {
        return $this->render('task/list.html.twig', ['tasks' => $repo->findBy(['isDone' => false])]);
    }

    /**
     * @Route("/tasks/done", name="app_task_list_done")
     * @param TaskRepository $repo
     * @return Response
     */
    public function listActionDone(TaskRepository $repo): Response
    {
        return $this->render('task/list.html.twig', ['tasks' => $repo->findBy(['isDone' => true])]);
    }

    /**
     * @Route("/tasks/create", name="app_task_create")
     *  @param  Request $request
     * @return Response
     */
    public function createAction(Request $request, TaskCreateHandler $handler): Response
    {
        $task = new Task();

        if ($handler->handle($request, $task)) {
            return $this->redirectToRoute('app_task_list');
        }

        return $this->render('task/create.html.twig', [
            'form' => $handler->getForm()->createView(),
        ]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="app_task_edit") : Response
     * @param  Task $task
     * @param  Request $request
     * @return Response
     */
    public function editAction(Task $task, Request $request, TaskEditHandler $handler): Response
    {
        if ($handler->handle($request, $task)) {
            return $this->redirectToRoute('app_task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $handler->getForm()->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="app_task_toggle")
     *  @param  Task $task
     * @return Response
     */
    public function toggleTaskAction(Task $task, TaskRepository $repo): Response
    {
        $repo->toggleTask($task);

        if (true === $task->isDone()) {
            return $this->redirectToRoute('app_task_list');
        }

        return $this->redirectToRoute('app_task_list_done');
    }

    /**
     * @Route("/tasks/{id}/delete", name="app_task_delete")
     * @param  Task $task
     * @return Response
     */
    public function deleteTaskAction(Task $task, TaskRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('delete', $task);

        $repo->deleteTask($task);

        return $this->redirectToRoute('app_task_list');
    }
}
