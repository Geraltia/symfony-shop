<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Test\ResetDatabase;

class AuthControllerTest extends WebTestCase
{
    use ResetDatabase;

    private function createTestUser(string $email, string $plainPassword, $container): void
    {
        $em = $container->get(EntityManagerInterface::class);
        $hasher = $container->get(UserPasswordHasherInterface::class);
        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($hasher->hashPassword($user, $plainPassword));
        $em->persist($user);
        $em->flush();
    }

    public function testSuccessfulLogin(): void
    {
        $client = static::createClient();
        $this->createTestUser('login@example.com', 'password123', static::getContainer());
        $client->request('POST', '/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'login@example.com',
            'password' => 'password123'
        ]));
        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
        $this->assertStringContainsString('Login successful', $client->getResponse()->getContent());
    }

    public function testLoginWithWrongPassword(): void
    {
        $client = static::createClient();
        $this->createTestUser('login2@example.com', 'password123', static::getContainer());
        $client->request('POST', '/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'login2@example.com',
            'password' => 'wrongpass'
        ]));
        $this->assertResponseStatusCodeSame(401);
        $this->assertStringContainsString('Invalid credentials', $client->getResponse()->getContent());
    }

    public function testLoginWithNonexistentEmail(): void
    {
        $client = static::createClient();
        $client->request('POST', '/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'notfound@example.com',
            'password' => 'password123'
        ]));
        $this->assertResponseStatusCodeSame(401);
        $this->assertStringContainsString('Invalid credentials', $client->getResponse()->getContent());
    }

    public function testLoginWithMissingFields(): void
    {
        $client = static::createClient();
        $client->request('POST', '/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => '',
            'password' => ''
        ]));
        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString('Email and password required', $client->getResponse()->getContent());
    }
}
