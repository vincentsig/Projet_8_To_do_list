<?php

namespace App\Service\FormHandler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use App\Service\FormHandler\AbstractHandlerInterface;

abstract class AbstractHandler implements AbstractHandlerInterface
{
    private FormFactoryInterface $formFactory;
    protected FormInterface $form;
    protected const FORMTYPE = 'abstract';

    /**
     * @required
     */
    public function setFormFactory(FormFactoryInterface $formFactory): void
    {
        $this->formFactory = $formFactory;
    }

    public function handle(Request $request, object $data, ?array $options = []): bool
    {
        $this->form = $this->formFactory->create(static::FORMTYPE, $data, $options)->handleRequest($request);

        if ($this->form->isSubmitted() && $this->form->isValid()) {
            $this->process($data);

            return true;
        }
        return false;
    }
}
