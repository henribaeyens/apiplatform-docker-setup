<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

class NotVerifiedException extends AccountStatusException
{
    public function getMessageKey(): string
    {
        return 'Account is not verified.';
    }
}
