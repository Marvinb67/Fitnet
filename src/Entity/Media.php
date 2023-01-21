<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use App\Entity\Trait\SlugTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\String\Slugger\SluggerInterface;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media
{
    use SlugTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $lien = null;

    #[ORM\ManyToMany(targetEntity: Publication::class, inversedBy: 'mediaPublication')]
    private Collection $publication;

    #[ORM\ManyToMany(targetEntity: Evenement::class, inversedBy: 'mediaEvenement')]
    private Collection $evenement;

    public function __construct()
    {
        $this->publication = new ArrayCollection();
        $this->evenement = new ArrayCollection();
    }

    #[PrePersist]
    public function prepesist(SluggerInterface $slugger)
    {
        $slugger->slug($this->titre);
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(string $lien): self
    {
        $this->lien = $lien;

        return $this;
    }

    /**
     * @return Collection<int, Publication>
     */
    public function getPublication(): Collection
    {
        return $this->publication;
    }

    public function addPublication(Publication $publication): self
    {
        if (!$this->publication->contains($publication)) {
            $this->publication->add($publication);
        }

        return $this;
    }

    public function removePublication(Publication $publication): self
    {
        $this->publication->removeElement($publication);

        return $this;
    }

    /**
     * @return Collection<int, Evenement>
     */
    public function getEvenement(): Collection
    {
        return $this->evenement;
    }

    public function addEvenement(Evenement $evenement): self
    {
        if (!$this->evenement->contains($evenement)) {
            $this->evenement->add($evenement);
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        $this->evenement->removeElement($evenement);

        return $this;
    }
}
