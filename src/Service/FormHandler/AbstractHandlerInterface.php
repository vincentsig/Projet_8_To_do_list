<?php

namespace App\Service\FormHandler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

interface AbstractHandlerInterface
{
    public function handle(Request $request, object $data, ?array $options = []): bool;

    public function getForm(): FormInterface;

    public function process(object $data): void;
}
