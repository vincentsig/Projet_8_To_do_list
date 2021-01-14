<?php

namespace App\Service;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

interface AbstractHandlerInterface
{
    public function getForm(): FormInterface;

    public function create(Request $request, object $data, array $options = []): bool;

    public function edit(Request $request, object $data, ?array $options = []): bool;

    public function delete(object $data): void;
}
