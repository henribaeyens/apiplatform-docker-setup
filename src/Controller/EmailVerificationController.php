<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Mailer;
use App\Entity\UserInterface;
use App\Dto\EmailVerificationDto;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function __invoke(
        #[MapRequestPayload] EmailVerificationDto $verificationData,
    ): JsonResponse
    {
        /** @var UserInterface $user */
        $user = $this->userRepository->findOneByEmailVerificationCode($verificationData->emailVerificationCode);
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

        if (null !== $auth) {
            return new JsonResponse(
                $auth->getContent(),
                Response::HTTP_OK,
                [
                    'content-type' => 'application/json',
                ],
                true
            );
        } else {
            return new JsonResponse([
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => 'Authentication is required',
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
