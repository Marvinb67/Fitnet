<?php

namespace App\Repository;

use Doctrine\ORM\Query;
use App\Data\SearchData;
use App\Entity\Publication;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Publication>
 *
 * @method Publication|null find($id, $lockMode = null, $lockVersion = null)
 * @method Publication|null findOneBy(array $criteria, array $orderBy = null)
 * @method Publication[]    findAll()
 * @method Publication[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Publication::class);
    }

    public function save(Publication $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Publication $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Recupére les produits en lien avec une recherche
     *
     * @return Publication[]
     */
    public function findSearch(SearchData $search): array
    {
        // return $this->findAll();
        $query = $this
            ->findActivePublicationQuery()
            ->select('u', 'p')
            ->join('p.user', 'u');
        if (!empty($search->getQ())) {
            $query = $query
                ->andWhere('p.titre LIKE :q')
                ->setParameter('q', "%{$search->getQ()}%");
        }
        // if (!empty($search->getAmis())) {
        //     $query = $query
        //         ->andWhere('u.id IN (:user)')
        //         ->setParameter('amis', $search->getAmis());
        // }
        // if (!empty($search->getDates())) {
        //     $query = $query
        //         ->andWhere('p.createdAt = :dates')
        //         ->setParameter('amis', $search->getDates());
        // }

        return $query->getQuery()->getResult();
    }

    /**
     * @return QueryBuilder
     */
    private function findActivePublicationQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = true');
    }
    /**
     * Trouver tous les Publications actives
     *
     * @return Query
     */
    public function findAllActivePublicationQuery(): Query
    {
        return $this->findActivePublicationQuery()
            ->getQuery();
    }
    /**
     * Trouver les 5 dernieres publications actives
     * @return Publication[]
     */
    public function findLatestActivePublication(): array
    {
        return $this->findActivePublicationQuery()
            ->setMaxResults(5)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
    /**
     * Trouver toutes les publications actives 
     * @return Publication[]
     */
    public function findAllActivePublication(): array
    {
        return $this->findActivePublicationQuery()
            ->getQuery()
            ->getResult();
    }



    //    /**
    //     * @return Publication[] Returns an array of Publication objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Publication
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
