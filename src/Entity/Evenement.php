<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use App\Entity\Trait\SlugTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\EditedAtTrait;
use Doctrine\ORM\Mapping\PreUpdate;
use App\Entity\Trait\CreatedAtTrait;
use Doctrine\ORM\Mapping\PrePersist;
use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\String\Slugger\SluggerInterface;

#[HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
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

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'evenements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'evenement', cascade: ['persist','remove'], targetEntity: ProgrammationEvenement::class)]
    private Collection $historiqueEvenements;

    #[ORM\ManyToMany(targetEntity: Media::class, mappedBy: 'evenement', cascade: ['persist'])]
    private Collection $mediaEvenement;

    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'evenement', cascade: ['persist', 'remove'])]
    private Collection $tagsEvenement;

    #[ORM\OneToMany(mappedBy: 'evenement', targetEntity: Commentaire::class, orphanRemoval: true)]
    private Collection $commentaires;

    public function __construct()
    {
        $this->historiqueEvenements = new ArrayCollection();
        $this->mediaEvenement = new ArrayCollection();
        $this->tagsEvenement = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
    }

    #[PrePersist]
    public function prepesist()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->editedAt = new \DateTimeImmutable();
        $this->slug = str_replace(' ', '-',trim(strtolower($this->intitule)));
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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
     * @return Collection<int, ProgrammationEvenement>
     */
    public function getHistoriqueEvenements(): Collection
    {
        return $this->historiqueEvenements;
    }

    public function addHistoriqueEvenement(ProgrammationEvenement $historiqueEvenement): self
    {
        if (!$this->historiqueEvenements->contains($historiqueEvenement)) {
            $this->historiqueEvenements->add($historiqueEvenement);
            $historiqueEvenement->setEvenement($this);
        }

        return $this;
    }

    public function removeHistoriqueEvenement(ProgrammationEvenement $historiqueEvenement): self
    {
        if ($this->historiqueEvenements->removeElement($historiqueEvenement)) {
            // set the owning side to null (unless already changed)
            if ($historiqueEvenement->getEvenement() === $this) {
                $historiqueEvenement->setEvenement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMediaEvenement(): Collection
    {
        return $this->mediaEvenement;
    }

    public function addMediaEvenement(Media $mediaEvenement): self
    {
        if (!$this->mediaEvenement->contains($mediaEvenement)) {
            $this->mediaEvenement->add($mediaEvenement);
            $mediaEvenement->addEvenement($this);
        }

        return $this;
    }

    public function removeMediaEvenement(Media $mediaEvenement): self
    {
        if ($this->mediaEvenement->removeElement($mediaEvenement)) {
            $mediaEvenement->removeEvenement($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTagsEvenement(): Collection
    {
        return $this->tagsEvenement;
    }

    public function addTagsEvenement(Tag $tagsEvenement): self
    {
        if (!$this->tagsEvenement->contains($tagsEvenement)) {
            $this->tagsEvenement->add($tagsEvenement);
            $tagsEvenement->addEvenement($this);
        }

        return $this;
    }

    public function removeTagsEvenement(Tag $tagsEvenement): self
    {
        if ($this->tagsEvenement->removeElement($tagsEvenement)) {
            $tagsEvenement->removeEvenement($this);
        }

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
            $commentaire->setEvenement($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getEvenement() === $this) {
                $commentaire->setEvenement(null);
            }
        }

        return $this;
    }
}
