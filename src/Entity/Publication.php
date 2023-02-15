<?php

namespace App\Entity;

use App\Entity\Media;
use App\Entity\Commentaire;
use Doctrine\DBAL\Types\Types;
use App\Entity\Trait\SlugTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\ReactionPublication;
use App\Entity\Trait\EditedAtTrait;
use Doctrine\ORM\Mapping\PreUpdate;
use App\Entity\Trait\CreatedAtTrait;
use Doctrine\ORM\Mapping\PrePersist;
use App\Repository\PublicationRepository;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: PublicationRepository::class)]
class Publication
{
    use CreatedAtTrait;
    use EditedAtTrait;
    use SlugTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('publication:read')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenu = null;

    #[ORM\Column(type: 'boolean')]
    private $isActive = true;

    #[ORM\ManyToOne(inversedBy: 'publications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'publication', targetEntity: Commentaire::class, cascade: ['remove'])]
    private Collection $commentaires;

    #[ORM\OneToMany(mappedBy: 'publication', targetEntity: ReactionPublication::class, orphanRemoval: true)]
    private Collection $reactionPublications;

    #[ORM\ManyToMany(targetEntity: Media::class, mappedBy: 'publication', cascade: ['persist'])]
    private Collection $mediaPublication;

    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'publication')]
    private Collection $tagsPublication;

    #[ORM\ManyToOne(inversedBy: 'publications')]
    private ?Groupe $groupe = null;



    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->reactionPublications = new ArrayCollection();
        $this->mediaPublication = new ArrayCollection();
        $this->tagsPublication = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function __toString()
    {
        $date = $this->getCreatedAt();
        return $date->format('Y');
    }

    #[PrePersist]
    public function prepesist()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->editedAt = new \DateTimeImmutable();
        //$this->slug = str_replace(' ', '-',trim(strtolower($this->titre)));
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

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

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

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setPublication($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getPublication() === $this) {
                $commentaire->setPublication(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ReactionPublication>
     */
    public function getReactionPublications(): Collection
    {
        return $this->reactionPublications;
    }

    public function addReactionPublication(ReactionPublication $reactionPublication): self
    {
        if (!$this->reactionPublications->contains($reactionPublication)) {
            $this->reactionPublications->add($reactionPublication);
            $reactionPublication->setPublication($this);
        }

        return $this;
    }

    public function removeReactionPublication(ReactionPublication $reactionPublication): self
    {
        if ($this->reactionPublications->removeElement($reactionPublication)) {
            // set the owning side to null (unless already changed)
            if ($reactionPublication->getPublication() === $this) {
                $reactionPublication->setPublication(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMediaPublication(): Collection
    {
        return $this->mediaPublication;
    }

    public function addMediaPublication(Media $mediaPublication): self
    {
        if (!$this->mediaPublication->contains($mediaPublication)) {
            $this->mediaPublication->add($mediaPublication);
            $mediaPublication->addPublication($this);
        }

        return $this;
    }

    public function removeMediaPublication(Media $mediaPublication): self
    {
        if ($this->mediaPublication->removeElement($mediaPublication)) {
            $mediaPublication->removePublication($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function gettagsPublication(): Collection
    {
        return $this->tagsPublication;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tagsPublication->contains($tag)) {
            $this->tagsPublication->add($tag);
            $tag->addPublication($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tagsPublication->removeElement($tag)) {
            $tag->removePublication($this);
        }

        return $this;
    }
    
    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getGroupe(): ?Groupe
    {
        return $this->groupe;
    }

    public function setGroupe(?Groupe $groupe): self
    {
        $this->groupe = $groupe;

        return $this;
    }

}
