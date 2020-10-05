<?php

namespace App\Repository;

use App\Entity\Answer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Answer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Answer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Answer[]    findAll()
 * @method Answer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Answer::class);
    }



    /**
     * check if already answered
     * @param $assignment_id
     * @param $content_id
     * @param $field_id
     * @return bool
     */
    public function isAlreadyAnswered($assignment_id, $content_id, $field_id){

        $qb = $this->createQueryBuilder('a')
            ->where('a.assignment = :assignment_id')
            ->andWhere('a.content = :content_id')
            ->andWhere('a.field = :field_id')
            ->setParameter(':assignment_id', $assignment_id)
            ->setParameter(':content_id', $content_id)
            ->setParameter(':field_id', $field_id)
            ->getQuery();

        return count($qb->getResult()) == 1;

    }


}
