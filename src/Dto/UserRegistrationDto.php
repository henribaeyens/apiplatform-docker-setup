<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class UserRegistrationDto
{
    public function __construct(
        #[Assert\NotBlank, Assert\Email]
        public ?string $email,

        #[Assert\NotBlank, Assert\Length(min: 6)]
        public ?string $password,

        #[Assert\NotBlank, Assert\Type(type: 'alpha')]
        public ?string $firstName,

        #[Assert\NotBlank, Assert\Type(type: 'alpha')]
        public ?string $lastName,
    ) {
    }

}
