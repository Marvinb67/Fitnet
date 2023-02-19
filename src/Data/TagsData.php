<?php

namespace App\Data;

use App\Entity\Tag;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

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
        $tags = explode(',' , $value);
        // array_walk($tags, static fn (string &$tagName): string => trim($tagName));
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