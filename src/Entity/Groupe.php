<?php

namespace App\Entity;

use App\Entity\Trait\SlugTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\EditedAtTrait;
use Doctrine\ORM\Mapping\PreUpdate;
use App\Entity\Trait\CreatedAtTrait;
use App\Repository\GroupeRepository;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

#[HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: GroupeRepository::class)]
class Groupe
{
    use CreatedAtTrait;
    use EditedAtTrait;
    use SlugTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $intitule = null;

    #[ORM\ManyToOne(inversedBy: 'groupes')]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\JoinTable(name: 'adherent_group')]
    private ?User $user = null;
    
    #[PrePersist]
    public function prepesist()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->editedAt = new \DateTimeImmutable();
    }

    #[PreUpdate]
    public function prepUp()
    {
        $this->editedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(string $intitule): self
    {
        $this->intitule = $intitule;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
