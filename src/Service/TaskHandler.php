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
    protected const FORMTYPE = TaskType::class;
    protected $security;

    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory, Security $security)
    {
        $this->em = $em;
        $this->formFactory  = $formFactory;
        $this->security = $security;
    }

    public function toggleTask(Task $task): void
    {
        $task->toggle(!$task->isDone());
        $this->em->flush();
    }
}
