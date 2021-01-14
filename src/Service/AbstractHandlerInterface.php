<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;

interface AbstractHandlerInterface
{
    public function handle(Request $request, object $data, ?array $options = []): bool;
}
