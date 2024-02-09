<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class EmailVerificationDto
{
    public function __construct(
        #[Assert\NotBlank, Assert\Type(type: 'digit'), Assert\Length(exactly: 6)]
        public ?string $emailVerificationCode,
    ) {
    }

}
