<?php
namespace App\Dto;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUserDto
{
    #[Assert\NotBlank(message: 'Email is required')]
    #[Assert\Email(message: 'Invalid email')]
    public ?string $email = null;

    #[Assert\NotBlank(message: 'Password is required')]
    #[Assert\Length(min: 6, minMessage: 'Password too short')]
    public ?string $password = null;

    public static function fromRequest(Request $request): self
    {
        $data = json_decode($request->getContent(), true);
        $dto = new self();
        $dto->email = $data['email'] ?? null;
        $dto->password = $data['password'] ?? null;
        return $dto;
    }
}

