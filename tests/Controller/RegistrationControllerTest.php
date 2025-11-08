<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use App\Factory\UserFactory;

class RegistrationControllerTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;

    public function testSuccessfulRegistration(): void
    {
        $client = static::createClient();
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

        UserFactory::createOne([
            'email' => 'test2@example.com',
            'password' => 'password123'
        ]);

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
        $client->request('POST', '/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => '',
            'password' => ''
        ]));
        $this->assertResponseStatusCodeSame(422);
        $this->assertStringContainsString('Password is required', $client->getResponse()->getContent());
        $this->assertStringContainsString('Email is required', $client->getResponse()->getContent());
    }

    public function testRegistrationWithInvalidEmail(): void
    {
        $client = static::createClient();
        $client->request('POST', '/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'invalid-email',
            'password' => 'password123'
        ]));
        $this->assertResponseStatusCodeSame(422);
        $this->assertStringContainsString('Invalid email', $client->getResponse()->getContent());
    }

    public function testRegistrationWithShortPassword(): void
    {
        $client = static::createClient();
        $client->request('POST', '/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'shortpass@example.com',
            'password' => '123'
        ]));
        $this->assertResponseStatusCodeSame(422);
        $this->assertStringContainsString('Password too short', $client->getResponse()->getContent());
    }
}