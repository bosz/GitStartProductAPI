<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Form\RegisterUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api', name: 'api_')]
class RegisterController extends AbstractController
{
    private $entityManager;
    private $userRepository;
    private $passwordHasher;
    private $formFactory;
    private $validator;
    private $jwtManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        FormFactoryInterface $formFactory,
        ValidatorInterface $validator,
        JWTTokenManagerInterface $jwtManager
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
        $this->formFactory = $formFactory;
        $this->validator = $validator;
        $this->jwtManager = $jwtManager;
    }

    #[Route('/register', name: 'user_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $user = new User();
        $form = $this->createForm(RegisterUserType::class, $user);
        $form->submit(json_decode($request->getContent(), true));


        if (!$form->isValid()) {
            $errors = $form->getErrors(true);
            $errorsString = (string) $errors;

            return new JsonResponse($errorsString, Response::HTTP_BAD_REQUEST);
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse('User registered successfully', Response::HTTP_CREATED);
    }

    #[Route('/login', name: 'user_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $credentials = json_decode($request->getContent(), true);
        $email = $credentials['email'];
        $password = $credentials['password'];

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse('Invalid credentials', Response::HTTP_UNAUTHORIZED);
        }

        $token = $this->jwtManager->create($user);

        return new JsonResponse([
            'token' => $token,
            'okay' => 'yes'
        ]);
    }
}
