<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\UserRole;
use App\Factory\UserFactoryInterface;
use App\Form\Type\UserRegistrationFormType;
use App\Helper\RandomnessGenerator;
use App\Model\UserRegistrationModel;
use App\Repository\UserRepository;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsController]
final class UserRegistrationController extends AbstractController
{
    public function __construct(
        private readonly UserFactoryInterface $userFactory,
        private readonly UserRepository $userRepository,
        private readonly TranslatorInterface $translator,
        private readonly RandomnessGenerator $randomnessGenerator,
        private readonly SerializerInterface $serializer,
        private readonly Mailer $mailer,
        private readonly ?CsrfTokenManagerInterface $csrfTokenManager = null
    ) {
    }

    #[Route('/register', name: 'user_registration', methods: ['POST'])]
    public function __invoke(Request $request): Response|JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(UserRegistrationFormType::class, new UserRegistrationModel());
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            /* @var UserInterface $user */
            try {
                $user = $this->userFactory->create(
                    firstName: $form->getData()->getFirstName(),
                    lastName: $form->getData()->getLastName(),
                    email: $form->getData()->getEmail(),
                    plainPassword: $form->getData()->getPassword(),
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
                'title' => 'There was a validation error',
                'errors' => $this->getErrorsFromForm($form),
            ], Response::HTTP_BAD_REQUEST
        );
    }

    private function getErrorsFromForm(FormInterface $form)
    {
        $errors = [];

        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }
}
