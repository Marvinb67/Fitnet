<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

Trait SlugTrait
{
    /**
     * Generation automatique du proriété slug pour l'utiliser dans les entites.
     *
     * @var \DateTimeImmutable|null
     */
    
    #[ORM\Column(type: 'string', length: 255)]
    private string $slug;
    
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

}