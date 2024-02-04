<?php

declare(strict_types=1);

namespace App\Command\User;

use App\Enum\UserRole;
use App\Factory\UserFactoryInterface;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function Symfony\Component\String\u;

/**
 * User creation command.
 */
#[AsCommand(
    name: 'app:user:create',
    description: 'Create a user.'
)]
class CreateUserCommand extends Command
{
    public const ARG_EMAIL = 'email';

    public const ARG_FIRSTNAME = 'firstname';

    public const ARG_LASTNAME = 'lastname';

    public const ARG_PASSWORD = 'password';

    public const ARG_IS_ADMIN = 'admin';

    private SymfonyStyle $io;

    public function __construct(
        private readonly UserFactoryInterface $userFactory,
        private readonly UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(self::ARG_FIRSTNAME, InputArgument::REQUIRED, 'The first name of the new user')
            ->addArgument(self::ARG_LASTNAME, InputArgument::REQUIRED, 'The last name of the new user')
            ->addArgument(self::ARG_EMAIL, InputArgument::REQUIRED, 'The email of the new user')
            ->addArgument(self::ARG_PASSWORD, InputArgument::REQUIRED, 'The password of the new user')
            ->addArgument(self::ARG_IS_ADMIN, InputArgument::REQUIRED, 'Is the new user an admin?')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $this->io->title('To create a user, answer the following questions:');

        $firstname = $this->io->ask('Enter user\'s first name', '', function (string $firstname): string {
            if (empty($firstname)) {
                throw new InvalidArgumentException('Cannot be empty.');
            }

            return $firstname;
        });

        $lastname = $this->io->ask('Enter user\'s last name', '', function (string $lastname): string {
            if (empty($lastname)) {
                throw new InvalidArgumentException('Cannot be empty.');
            }

            return $lastname;
        });

        $email = $this->io->ask('Enter user\'s email', '', function (string $email): string {
            if (empty($email)) {
                throw new InvalidArgumentException('Cannot be empty.');
            }
            if (null === u($email)->indexOf('@')) {
                throw new InvalidArgumentException('The email should look like a real email.');
            }

            return $email;
        });

        $password = $this->io->ask('Enter user\'s password', '', function (string $password): string {
            if (empty($password)) {
                throw new InvalidArgumentException('Cannot be empty.');
            }

            return $password;
        });

        $isAdmin = $this->io->choice('Is this user an admin?', ['Yes', 'No'], 'No');

        $input->setArgument(self::ARG_FIRSTNAME, $firstname);
        $input->setArgument(self::ARG_LASTNAME, $lastname);
        $input->setArgument(self::ARG_EMAIL, $email);
        $input->setArgument(self::ARG_PASSWORD, $password);
        $input->setArgument(self::ARG_IS_ADMIN, $isAdmin);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $firstname */
        $firstname = $input->getArgument(self::ARG_FIRSTNAME);
        /** @var string $lastname */
        $lastname = $input->getArgument(self::ARG_LASTNAME);
        /** @var string $email */
        $email = $input->getArgument(self::ARG_EMAIL);
        /** @var string $plainPassword */
        $plainPassword = $input->getArgument(self::ARG_PASSWORD);
        /** @var string $isAdmin */
        $isAdmin = $input->getArgument(self::ARG_IS_ADMIN);

        /* @var UserInterface $user */
        try {
            $user = $this->userFactory->create(
                firstName: $firstname,
                lastName: $lastname,
                email: $email,
                plainPassword: $plainPassword,
                roles: ['Yes' === $isAdmin ? UserRole::ADMIN->value : UserRole::USER->value],
                verified: 'Yes' === $isAdmin
            );
        } catch (\RuntimeException $e) {
            $this->io->error(
                $e->getMessage()
            );

            return Command::FAILURE;
        }
        $this->userRepository->save($user);

        $this->io->success(
            sprintf(
                '%s was successfully created: %s (%s)',
                'Yes' === $isAdmin ? 'Administrator' : 'User',
                $user->getFirstName().' '.$user->getLastName(),
                $user->getEmail()
            )
        );

        return Command::SUCCESS;
    }
}
