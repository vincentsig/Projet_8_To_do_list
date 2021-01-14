<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;

abstract class AbstractHandler implements AbstractHandlerInterface
{

    public function handle(Request $request, object $data, ?array $options = []): bool
    {
        $this->form = $this->formFactory->create(static::FORMTYPE, $data, $options = [])->handleRequest($request);

        if ($this->form->isSubmitted() && $this->form->isValid()) {
            $this->data = $data;
            return true;
        }
        return false;
    }
}
