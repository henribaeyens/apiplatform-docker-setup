<?php

declare(strict_types=1);

namespace App\Model;

class ResetPasswordModel
{
    protected $plainPassword;

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

}
