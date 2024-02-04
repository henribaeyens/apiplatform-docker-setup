<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface as CoreUserInterface;

interface UserInterface extends CoreUserInterface
{
    public function __toString(): string;

    public function getPlainPassword(): ?string;

    public function setPassword(string $password): static;

    public function getFirstName(): ?string;

    public function getLastName(): ?string;

    public function getEmail(): ?string;

    public function setEmailVerificationCode(?string $emailVerificationCode): static;

    public function getEmailVerificationCode(): ?string;

    public function setVerified(bool $verified): static;

    public function isVerified(): bool;

    public function setRecoveryToken(?string $recoveryToken): static;

    public function getRecoveryToken(): ?string;

    public function setRecoveryRequestDate(?\DateTimeInterface $recoveryRequestDate): static;

    public function getRecoveryRequestDate(): ?\DateTimeInterface;

    public function isRecoveryRequestExpired(int $ttl): bool;
}
