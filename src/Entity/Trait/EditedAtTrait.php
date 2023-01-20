<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

Trait EditedAtTrait
{
    /**
     * Generation automatique du proriété createdAt pour l'utiliser dans les entites.
     *
     * @var \DateTimeImmutable|null
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $editedAt = null;

    public function getEditedAt(): ?\DateTimeImmutable
    {
        return $this->editedAt;
    }

    public function setEditedAt(\DateTimeImmutable $editedAt): self
    {
        $this->editedAt = $editedAt;

        return $this;
    }

}