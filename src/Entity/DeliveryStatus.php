<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\DeliveryStatusRepository;

#[ORM\Entity(repositoryClass: DeliveryStatusRepository::class)]
class DeliveryStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 50)]
    private string $name;

}

