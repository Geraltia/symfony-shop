<?php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    private function validateRegistrationData(?string $email, ?string $password, EntityManagerInterface $em): ?array
    {
        if (!$email || !$password) {
            return ['error' => 'Email and password required', 'code' => Response::HTTP_BAD_REQUEST];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['error' => 'Invalid email', 'code' => Response::HTTP_BAD_REQUEST];
        }
        if (strlen($password) < 6) {
            return ['error' => 'Password too short', 'code' => Response::HTTP_BAD_REQUEST];
        }
        if ($em->getRepository(User::class)->findOneBy(['email' => $email])) {
            return ['error' => 'Email already exists', 'code' => Response::HTTP_CONFLICT];
        }
        return null;
    }

    #[Route('/register', name: 'user_register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        $validationError = $this->validateRegistrationData($email, $password, $em);
        if ($validationError) {
            return $this->json(['error' => $validationError['error']], $validationError['code']);
        }

        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($passwordHasher->hashPassword($user, $password));

        $em->persist($user);
        $em->flush();

        return $this->json(['message' => 'User registered successfully'], Response::HTTP_CREATED);
    }
}
