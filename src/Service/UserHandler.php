<?php

namespace App\Service;

use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormFactoryInterface;

class UserHandler extends AbstractHandler
{
    protected EntityManagerInterface $em;
    protected FormInterface $form;
    const FORMTYPE = UserType::class;

    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory)
    {
        $this->em = $em;
        $this->formFactory  = $formFactory;
    }

}
