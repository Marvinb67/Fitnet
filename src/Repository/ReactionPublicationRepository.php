<?php

namespace App\Repository;

use App\Entity\Publication;
use App\Entity\ReactionPublication;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReactionPublication>
 *
 * @method ReactionPublication|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReactionPublication|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReactionPublication[]    findAll()
 * @method ReactionPublication[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReactionPublicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReactionPublication::class);
    }

    public function save(ReactionPublication $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ReactionPublication $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countByPublicationAndUser(User $user, Publication $publication)
    {
        $query = $this->createQueryBuilder('r')
            ->select('COUNT(r)')
            ->where('r.publication = :publication')
            ->andWhere('r.user = :user')
            ->setParameter('user', $user)
            ->setParameter('publication', $publication);
            return $query->getQuery()->getSingleScalarResult();

    }

    public function myReactionToPublication(User $user, Publication $publication)
    {
        $query = $this->createQueryBuilder('r')
            ->select('r.etatLikeDislike')->distinct()
            ->where('r.publication = :publication')
            ->andWhere('r.user = :user')
            ->setParameter('user', $user)
            ->setParameter('publication', $publication);
            return $query->getQuery()->getResult();
    }

    public function countByPublicationLikes($publication, $etatLikeDislike){
        $query = $this->createQueryBuilder('r')
            ->select('COUNT(r)')->distinct()
            ->where('r.publication = :publication')
            ->andWhere('r.etatLikeDislike = :etatLikeDislike')
            ->setParameter('publication', $publication)
            ->setParameter('etatLikeDislike', $etatLikeDislike);
            return $query->getQuery()->getSingleScalarResult();
    }
//    /**
//     * @return ReactionPublication[] Returns an array of ReactionPublication objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ReactionPublication
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
