<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

Trait CreatedAtTrait
{
    /**
     * Generation automatique du proriété createdAt pour l'utiliser dans les entites.
     *
     * @var \DateTimeImmutable|null
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}