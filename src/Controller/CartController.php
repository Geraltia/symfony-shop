<?php
namespace App\Controller;

use App\Entity\Cart;
use App\Repository\CartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class CartController extends AbstractController
{
    #[Route('/cart', methods: ['POST'])]
    public function create(EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if (!$user || !$user instanceof \App\Entity\User) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }
        $cart = new Cart();
        $cart->setUser($user);
        $em->persist($cart);
        $em->flush();

        return $this->json(['id' => $cart->getId()]);
    }

    #[Route('/cart/{id}', methods: ['GET'])]
    public function show(CartRepository $cartRepository, int $id): JsonResponse
    {
        $cart = $cartRepository->find($id);
        if (!$cart) {
            return $this->json(['error' => 'Cart not found'], 404);
        }
        return $this->json([
            'id' => $cart->getId(),
            'user' => $cart->getUser()->getId(),
            'createdAt' => $cart->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
    }
}
