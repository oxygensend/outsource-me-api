<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

abstract class AbstractEntity
{

    use TimestampableEntity;

    #[ApiProperty]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:profile', 'language:read', 'job_position:read','application:users', 'education:read', 'user:get', 'jobOffer:get', 'jobOffer:one', 'work_type:get', 'technologies:get', 'user:profile-developer', 'address:list','attachment:one'])]
    protected ?int $id = null;

    public function __construct() {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }


}
