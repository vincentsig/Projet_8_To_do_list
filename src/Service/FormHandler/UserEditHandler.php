<?php

namespace App\Service\FormHandler;

use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class UserEditHandler extends AbstractHandler
{
    private EntityManagerInterface $em;
    private SessionInterface $session;
    protected const FORMTYPE = UserType::class;

    public function __construct(EntityManagerInterface $em, SessionInterface $session)
    {
        $this->em = $em;
        $this->session = $session;
    }

    public function process(object $data): void
    {
        $this->em->flush();

        $this->session->getFlashBag()->add('success', "L'utilisateur a bien été modifié");
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }
}
