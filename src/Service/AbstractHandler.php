<?php

namespace App\Service;

use App\Entity\Task;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractHandler implements AbstractHandlerInterface
{


    protected function createForm(Request $request, $data): FormInterface
    {
        return $this->formFactory->create(static::FORMTYPE, $data, $options = [])->handleRequest($request);
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }

    public function create(Request $request, object $data, array $options = []): bool
    {
        $this->form = $this->createForm($request, $data);

        if ($this->form->isSubmitted() && $this->form->isValid()) {
            if ($data instanceof Task) {
                $data->setAuthor($this->security->getUser());
            }
            $this->em->persist($data);
            $this->em->flush();

            return true;
        }
        return false;
    }

    public function edit(Request $request, object $data, ?array $options = []): bool
    {
        $this->form = $this->createForm($request, $data);

        if ($this->form->isSubmitted() && $this->form->isValid()) {
            $this->em->persist($data);
            $this->em->flush();

            return true;
        }
        return false;
    }

    public function delete(object $data): void
    {
        $this->em->remove($data);
        $this->em->flush();
    }
}
