<?php

namespace App\Factory;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public static function class(): string
    {
        return User::class;
    }

    protected function defaults(): array
    {
        return [
            'email' => self::faker()->email(),
            'password' => 'password123',
            'roles' => ['ROLE_USER'],
        ];
    }

    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function(User $user): void {
                if ($user->getPassword()) {
                    $user->setPassword(
                        $this->passwordHasher->hashPassword($user, $user->getPassword())
                    );
                }
            })
            ;
    }
}