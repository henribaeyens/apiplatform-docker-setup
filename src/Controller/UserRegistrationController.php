<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\UserRegistrationDto;
use App\Enum\UserRole;
use App\Factory\UserFactoryInterface;
use App\Helper\RandomnessGenerator;
use App\Repository\UserRepository;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
final class UserRegistrationController extends AbstractController
{
    public function __construct(
        private readonly UserFactoryInterface $userFactory,
        private readonly UserRepository $userRepository,
        private readonly RandomnessGenerator $randomnessGenerator,
        private readonly SerializerInterface $serializer,
        private readonly Mailer $mailer,
    ) {
    }

    #[Route('/register', name: 'user_registration', methods: ['POST'])]
    public function __invoke(
        #[MapRequestPayload] UserRegistrationDto $signupData,
    ): JsonResponse {
        /* @var UserInterface $user */
        $user = $this->userFactory->create(
            firstName: $signupData->firstName,
            lastName: $signupData->lastName,
            email: $signupData->email,
            plainPassword: $signupData->password,
            roles: [UserRole::USER->value],
        );

        $user->setEmailVerificationCode($this->randomnessGenerator->generateNumeric(6));
        $this->userRepository->save($user);
        $this->mailer->sendEmailVerificationCode($user);

        $context = (new ObjectNormalizerContextBuilder())
            ->withGroups(['user:read'])
            ->toArray()
        ;

        return new JsonResponse(
            $this->serializer->serialize($user, 'json', $context),
            Response::HTTP_CREATED,
            [
                'content-type' => 'application/json',
            ],
            true
        );
    }
}
