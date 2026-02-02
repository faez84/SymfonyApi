<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\User;

interface DeleteUserHandlerInterface
{
    public function handle(int $id): void;
}
