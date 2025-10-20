<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class RegistrationControllerTest extends WebTestCase
{
    public function testSuccessfulRegistration(): void
    {
        $client = static::createClient();
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->createQuery('DELETE FROM App\\Entity\\User')->execute();
        $client->request('POST', '/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'test@example.com',
            'password' => 'password123'
        ]));

        $this->assertResponseStatusCodeSame(201);
        $this->assertJson($client->getResponse()->getContent());
        $this->assertStringContainsString('User registered successfully', $client->getResponse()->getContent());
    }

    public function testRegistrationWithExistingEmail(): void
    {
        $client = static::createClient();
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->createQuery('DELETE FROM App\\Entity\\User')->execute();
        // Первый запрос - успешная регистрация
        $client->request('POST', '/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'test2@example.com',
            'password' => 'password123'
        ]));
        $this->assertResponseStatusCodeSame(201);
        // Второй запрос - email уже существует
        $client->request('POST', '/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'test2@example.com',
            'password' => 'password123'
        ]));
        $this->assertResponseStatusCodeSame(409);
        $this->assertStringContainsString('Email already exists', $client->getResponse()->getContent());
    }

    public function testRegistrationWithMissingFields(): void
    {
        $client = static::createClient();
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->createQuery('DELETE FROM App\\Entity\\User')->execute();
        $client->request('POST', '/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => '',
            'password' => ''
        ]));
        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString('Email and password required', $client->getResponse()->getContent());
    }

    public function testRegistrationWithInvalidEmail(): void
    {
        $client = static::createClient();
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->createQuery('DELETE FROM App\\Entity\\User')->execute();
        $client->request('POST', '/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'invalid-email',
            'password' => 'password123'
        ]));
        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString('Invalid email', $client->getResponse()->getContent());
    }

    public function testRegistrationWithShortPassword(): void
    {
        $client = static::createClient();
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->createQuery('DELETE FROM App\\Entity\\User')->execute();
        $client->request('POST', '/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'shortpass@example.com',
            'password' => '123'
        ]));
        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString('Password too short', $client->getResponse()->getContent());
    }


}
