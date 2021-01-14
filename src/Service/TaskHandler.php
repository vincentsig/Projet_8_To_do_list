<?php

namespace App\Service;

use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormFactoryInterface;

class TaskHandler extends AbstractHandler
{
    protected EntityManagerInterface $em;
    protected FormInterface $form;
    protected object $data;
    protected const FORMTYPE = TaskType::class;
    protected Security $security;

    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory, Security $security)
    {
        $this->em = $em;
        $this->formFactory  = $formFactory;
        $this->security = $security;
    }

    public function createTask(): void
    {
        $this->data->setAuthor($this->security->getUser());
        $this->em->persist($this->data);
        $this->em->flush();
    }

    public function editTask(): void
    {
        $this->em->persist($this->data);
        $this->em->flush();
    }

    public function deleteTask(object $data): void
    {
        $this->em->remove($data);
        $this->em->flush();
    }

    public function toggleTask(Task $task): void
    {
        $task->toggle(!$task->isDone());
        $this->em->flush();
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }
}
