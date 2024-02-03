<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsController]
final class EmailVerificationController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly Mailer $mailer,
        private readonly Security $security
    ) {
    }

    #[Route('/email_verification', name: 'email_verification', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        /** @var UserInterface $user */
        $user = $this->userRepository->findOneByEmailVerificationCode($data['emailVerificationCode']);
        if (null === $user) {
            return new JsonResponse([
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => 'no match found',
            ], Response::HTTP_BAD_REQUEST);
        }
        $user->setVerified(true);
        $user->setEmailVerificationCode(null);
        $this->userRepository->save($user);
        $this->mailer->sendUserVerifiedNotification($user);
        $this->mailer->sendUserVWelcomeMessage($user);

        $auth = $this->security->login($user, 'json_login', 'auth');

        return new JsonResponse(
            $auth->getContent(),
            Response::HTTP_OK,
            [
                'content-type' => 'application/json',
            ],
            true
        );
    }
}
