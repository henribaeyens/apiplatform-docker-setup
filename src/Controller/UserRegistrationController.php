<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\UserRole;
use App\Factory\UserFactoryInterface;
use App\Form\Type\UserRegistrationFormType;
use App\Helper\FormHelper;
use App\Helper\RandomnessGenerator;
use App\Model\UserRegistrationModel;
use App\Repository\UserRepository;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
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
    public function __invoke(Request $request): Response|JsonResponse
    {
        $data = (array) json_decode($request->getContent(), true);
        $form = $this->createForm(UserRegistrationFormType::class, new UserRegistrationModel());

        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            /* @var UserRegistrationModel $formData */
            $formData = $form->getData();
            /* @var UserInterface $user */
            try {
                $user = $this->userFactory->create(
                    // phpstan throwing false positives for the next 4 lines
                    firstName: $formData->getFirstName(),
                    lastName: $formData->getLastName(),
                    email: $formData->getEmail(),
                    plainPassword: $formData->getPassword(),
                    roles: [UserRole::USER->value],
                );
            } catch (\RuntimeException $e) {
                return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
            }

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

        return new JsonResponse([
                'type' => 'validation_error',
                'title' => 'The form contains one or more errors',
                'errors' => FormHelper::getFormErrors($form),
            ], Response::HTTP_BAD_REQUEST
        );
    }
}
