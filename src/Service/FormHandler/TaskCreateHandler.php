<?php

namespace App\Service\FormHandler;

use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

class TaskCreateHandler extends AbstractHandler
{
    private EntityManagerInterface $em;
    protected const FORMTYPE = TaskType::class;
    private Security $security;
    private SessionInterface $session;

    public function __construct(EntityManagerInterface $em, Security $security, SessionInterface $session)
    {
        $this->em = $em;
        $this->security = $security;
        $this->session = $session;
    }

    public function process(object $data): void
    {
        $data->setAuthor($this->security->getUser());
        $this->em->persist($data);
        $this->em->flush();

        $this->session->getFlashBag()->add('success', 'La tâche a bien été ajoutée.');
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }
}
