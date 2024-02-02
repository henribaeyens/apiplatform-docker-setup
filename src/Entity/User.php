<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\Timestampable;
use App\Enum\UserRole;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\State\UserPasswordHasher;
use App\Repository\UserRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Post(
            security: "is_granted('ROLE_ADMIN')",
            processor: UserPasswordHasher::class,
            validationContext: ['groups' => ['user:create']]
        ),
        new Get(
            security: "is_granted('ROLE_ADMIN') or object.owner == user",
        ),
        new Put(
            security: "is_granted('ROLE_ADMIN') or object.owner == user",
            processor: UserPasswordHasher::class
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN') or object.owner == user",
            processor: UserPasswordHasher::class
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')"
        ),
    ],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:create', 'user:update']],
)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
    #[Groups(['user:read', 'user:create', 'user:update'])]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: Types::STRING)]
    private ?string $password = null;

    #[Assert\NotBlank(groups: ['user:create'])]
    #[Groups(['user:create', 'user:update'])]
    private ?string $plainPassword = null;

    #[ORM\Column(type: Types::STRING, length: 64)]
    #[Groups(['user:read', 'user:create', 'user:update'])]
    #[Assert\NotBlank]
    private ?string $firstName = null;

    #[ORM\Column(type: Types::STRING, length: 64)]
    #[Groups(['user:read', 'user:create', 'user:update'])]
    #[Assert\NotBlank]
    private ?string $lastName = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $verified = false;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $recoveryToken = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $recoveryRequestDate = null;

    #[ORM\Column(length: 6, nullable: true)]
    #[Groups(['user:read'])]
    private ?string $emailVerificationCode = null;

    use Timestampable;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function __toString(): string
    {
        return "{$this->firstName} {$this->lastName} ({$this->email})";
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee user at least has ROLE_USER if none is set
        if (empty($roles)) {
            $roles[] = UserRole::USER->value;
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles);
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmailVerificationCode(): ?string
    {
        return $this->emailVerificationCode;
    }

    public function setEmailVerificationCode(?string $emailVerificationCode): static
    {
        $this->emailVerificationCode = $emailVerificationCode;

        return $this;
    }

    public function isVerified(): bool
    {
        return true === $this->verified;
    }

    public function setVerified(bool $verified): static
    {
        $this->verified = $verified;

        return $this;
    }

    public function getRecoveryToken(): ?string
    {
        return $this->recoveryToken;
    }

    public function setRecoveryToken(?string $recoveryToken): static
    {
        $this->recoveryToken = $recoveryToken;

        return $this;
    }

    public function getRecoveryRequestDate(): ?\DateTimeInterface
    {
        return $this->recoveryRequestDate;
    }

    public function setRecoveryRequestDate(?\DateTimeInterface $recoveryRequestDate): static
    {
        $this->recoveryRequestDate = $recoveryRequestDate;

        return $this;
    }

    public function isRecoveryRequestExpired(int $ttl): bool
    {
        $passwordRequestedAt = $this->getRecoveryRequestDate();

        return null !== $passwordRequestedAt && $passwordRequestedAt->getTimestamp() + $ttl > time();
    }
}
