<?php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Dto\LoginRequestDto;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

class AuthController extends AbstractController
{
    #[Route('/login', name: 'user_login', methods: ['POST'],format: 'json')]
    public function login(
        #[MapRequestPayload] LoginRequestDto $dto,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        $user = $em->getRepository(User::class)->findOneBy(['email' => $dto->email]);
        if (!$user || !$passwordHasher->isPasswordValid($user, $dto->password)) {
            return $this->json(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json(['message' => 'Login successful']);
    }
}
