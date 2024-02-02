<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Mailer;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsController]
final class EmailVerificationController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly Mailer $mailer,
    ) {
    }

    #[Route('/email_verification', name: 'email_verification', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        /** @var UserInterface $user */
        $user = $this->userRepository->findOneByEmailVerificationCode($data['emailVerificationCode']);
        if (null === $user) {
            return new Response('no match', Response::HTTP_BAD_REQUEST);
        }
        $user->setVerified(true);
        $user->setEmailVerificationCode(null);
        $this->userRepository->save($user);
        $this->mailer->sendUserVerifiedNotification($user);
        $this->mailer->sendUserVWelcomeMessage($user);

        return new Response('user verified', Response::HTTP_OK);
    }
}
