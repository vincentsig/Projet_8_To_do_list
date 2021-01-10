<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{
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
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $task->setAuthor($this->getUser());
            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a bien été ajoutée.');

            return $this->redirectToRoute('app_task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
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
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('app_task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
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
        $task->toggle(!$task->isDone());
        $this->getDoctrine()->getManager()->flush();

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
        $em = $this->getDoctrine()->getManager();
        $em->remove($task);
        $em->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('app_task_list');
    }
}
