<?php

namespace App\Repository;

use Doctrine\ORM\Query;
use App\Data\SearchData;
use App\Entity\Publication;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

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
     * Recupére les publications en lien avec une recherche
     *
     * @return Publication[]
     */
    public function findSearch(SearchData $search, User $user, int $limit = 6): array
    {
        $query = $this
            ->findActivePublicationQuery()
            ->select('u', 'p')
            ->join('p.user', 'u')
            ->leftJoin('u.amis', 'a')
            ->leftJoin('u.followedByUsers', 'f')
            ->where('a.id = :userId OR f.id = :userId')
            ->setParameter('userId', $user->getId())
            ->setMaxResults($limit)
            ->setFirstResult(($search->getPage() * $limit) - $limit);

        if (!empty($search->getQ())) {
            $query = $query->andWhere('p.titre LIKE :q OR p.contenu LIKE :q')
                ->setParameter('q', "%{$search->getQ()}%");
            // les mots a chercher exploser la chaine de string et les mettre dans un tableau
            $mots = explode(" ", $search->getQ());
            // parcourir le tableau de mots
            for ($i = 0; $i < count($mots); $i++) {
                // accepter la recherche seulement si le mot a plus de 2 lettres
                if (strlen($mots[$i]) > 2) {
                    // si le compteur est a zero ajouter WHERE a la requete
                    $query = $query->orWhere($query->expr()->orX(
                        $query->expr()->like('p.titre',  ':q' . $i),
                        // $query->expr()->like('p.contenu',  ':q' . $i)
                    ))->setParameter('q' . $i, "%{$mots[$i]}%");
                }
            }
        }
        if (!empty($search->getTag())) {
            $query = $query
                ->innerJoin('p.tagsPublication ', 'tp')
                ->andWhere('tp.intitule = :tag')
                ->setParameter('tag', $search->getTag());
            // dd($query);
        }
        // if (!empty($search->getDates())) {
        //     $query = $query
        //         ->andWhere('p.createdAt = :dates')
        //         ->setParameter('amis', $search->getDates());
        // }
        $paginator = new Paginator($query);
        $data = $paginator->getQuery()->getResult();
        if (empty($data)) {
            return [];
        }
        $pages = ceil($paginator->count() / $limit);
        $result = [
            'data' => $data,
            'pages' => $pages,
            'limit' => $limit,
            'page' => $search->getPage(),
            'q' => $search->getQ(),
            'tag' => $search->getTag(),
        ];
        return $result;
    }

    /**
     * @return QueryBuilder
     */
    private function findActivePublicationQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where('p.isActive = true')
            ->orderBy('p.createdAt', 'DESC');
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

    public function findActivePublicationsOfFriends(User $user): array
    {
        $queryBuilder = $this->findActivePublicationQuery();

        $queryBuilder->innerJoin('p.user', 'u')
            ->leftJoin('u.amis', 'a')
            ->leftJoin('u.followedByUsers', 'f')
            ->where('a.id = :userId OR f.id = :userId')
            ->setParameter('userId', $user->getId());

        return $queryBuilder->getQuery()->getResult();
    }

    // public function findPublicationsPaginated(int $page, string $slug, int $limit = 8): array
    // {
    //     $limit =abs($limit);
    //     $result = [];

    //     $query = $this->findActivePublicationQuery()
    //                     ->
    //     return $result;
    // }
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
