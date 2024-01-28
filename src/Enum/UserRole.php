<?php

declare(strict_types=1);

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\Translation\TranslatableInterface;

enum UserRole: string implements TranslatableInterface {
    case USER = 'ROLE_USER';
    case ADMIN = 'ROLE_ADMIN';

    public function trans(TranslatorInterface $translator, string $locale = null): string
    {
        return match ($this) {
            self::USER  => $translator->trans('form.choices.roles.user', locale: $locale),
            self::ADMIN => $translator->trans('form.choices.roles.admin', locale: $locale),
        };
    }
}