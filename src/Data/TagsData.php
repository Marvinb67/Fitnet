<?php

namespace App\Data;

use App\Entity\Tag;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use function Symfony\Component\String\u;

final class TagsData implements DataTransformerInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {}

    /**
     *
     * @param Collection<int, Tag> $value
     * @return string
     */
    public function transform(mixed $value) :string
    {
        if ($value === null) {
            return '';
        }
        return implode(',', $value->map(static fn (Tag $tag): string => $tag->getIntitule())->toArray());
    }
/**
     *
     * @param string $value
     * @return Collection<int, Tag>
     */
    public function reverseTransform(mixed $value): Collection
    {
        /**
         * La fonction "u" s'assure que toutes les comparaisons de chaînes se font sur leur 
         * représentation canoniquement composée.
         * En dissocier la chaines par les vergules
         */
        $tags = u($value)->split(',');
        /**
         * array_walk — Exécute une fonction fournie par l'utilisateur sur chacun des éléments d'un tableau.
         * trim supprime les vide et à la fin de chaque tag
         */
        array_walk($tags, static fn (string &$tagName): string => u($tagName)->trim()->toString());
        /**
         * Persistance et enregistrement des données ou utilisation des tag s'ils exit déjà
         */
        $tagsCollection = new ArrayCollection();

        $tagRepo = $this->entityManager->getRepository(Tag::class);

        foreach ($tags as $tagName) {
            if ($tagName === '') {
                continue;
            }
           $tag = $tagRepo->findOneBy(['intitule' => $tagName]);

           if ($tagName === null) {
            $tag = new Tag();
            $tag->setIntitule($tagName);
            $this->entityManager->persist($tag);
           }
           $tagsCollection->add($tag);
        }
        return $tagsCollection;
    }
}