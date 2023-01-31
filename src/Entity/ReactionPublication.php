<?php

namespace App\Entity;

use App\Repository\ReactionPublicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReactionPublicationRepository::class)]
class ReactionPublication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $etatLikeDislike = null;

    #[ORM\ManyToOne(inversedBy: 'reactionPublications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'reactionPublications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Publication $publication = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isEtatLikeDislike(): ?bool
    {
        return $this->etatLikeDislike;
    }

    public function setEtatLikeDislike(?bool $etatLikeDislike): self
    {
        $this->etatLikeDislike = $etatLikeDislike;

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

    public function getPublication(): ?Publication
    {
        return $this->publication;
    }

    public function setPublication(?Publication $publication): self
    {
        $this->publication = $publication;

        return $this;
    }
}
