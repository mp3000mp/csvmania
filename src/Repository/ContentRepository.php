<?php

namespace App\Repository;

use App\Entity\Content;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Content|null find($id, $lockMode = null, $lockVersion = null)
 * @method Content|null findOneBy(array $criteria, array $orderBy = null)
 * @method Content[]    findAll()
 * @method Content[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Content::class);
    }



    /**
     * get next field to entry
     * @param $assignment_id
     * @return mixed
     */
    public function getNextEntry($assignment_id){
        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->innerJoin('c.task', 't')
            ->addSelect('t')
            ->innerJoin('t.assignments', 'a')
            ->addSelect('a')
            ->innerJoin('t.fields', 'f')
            ->addSelect('f')
            ->innerJoin('f.feeling', 'fe')
            ->addSelect('fe')
            ->leftJoin('f.answers', 'an', 'WITH', 'an.content = c AND an.assignment = a')
            ->andWhere('an.id IS NULL')
            ->andWhere('a.id = :assingment_id')
            ->addOrderBy('c.id','ASC')
            ->addOrderBy('f.id','ASC')
            ->setParameter('assingment_id', $assignment_id)
            ->setMaxResults(1)
            ->getQuery();

        return $qb->getResult();
    }


}
