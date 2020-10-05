<?php

namespace App\Repository;

use App\Entity\Assignment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Assignment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Assignment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Assignment[]    findAll()
 * @method Assignment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssignmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Assignment::class);
    }


    /**
     * get nb answers, contents and fields
     * @param $assignment_id
     * @return mixed
     */
    public function getAssignmentProgressionDone($assignment_id){

        $qb = $this->createQueryBuilder('a')
            ->innerJoin('a.task', 't')
            ->leftJoin('a.answers', 'w')
            ->select('COUNT(DISTINCT w.id) AS nb_answers')
            ->innerJoin('t.contents', 'c')
            ->addSelect('COUNT(DISTINCT c.id) AS nb_contents')
            ->innerJoin('t.fields', 'f')
            ->addSelect('COUNT(DISTINCT f.id) AS nb_fields')
            ->andWhere('a.id = :assignment_id')
            ->setParameter(':assignment_id', $assignment_id)
            ->groupBy('a.id')
            ->getQuery();
        return $qb->getSingleResult();

    }


    /**
     * trouve les tâches/assignment en cours de saisie par le user + comptes pour progression
     * @param $user_id
     * @return array => [ [Task with Assignments, nb_answers, nb_contents, nb_fields], [...] ]
     */
    public function getOngoingAssignments($user_id){

        $qb = $this->createQueryBuilder('a')
            ->innerJoin('a.task', 't')
            ->addSelect('t')
            ->leftJoin('a.answers', 'w')
            ->addSelect('COUNT(DISTINCT w.id) AS nb_answers')
            ->innerJoin('t.contents', 'c')
            ->addSelect('COUNT(DISTINCT c.id) AS nb_contents')
            ->innerJoin('t.fields', 'f')
            ->addSelect('COUNT(DISTINCT f.id) AS nb_fields')
            ->where('a.status = 0')
            ->andWhere('a.assigned_to = :user_id')
            ->setParameter(':user_id', $user_id)
            ->groupBy('a.id')
            ->orderBy('t.priority', 'ASC')
            ->addOrderBy('t.deadline', 'ASC')
            ->addOrderBy('t.created_at', 'ASC')
            ->getQuery();
        return $qb->getResult();

    }


    /**
     * get nb assignments attached to task
     * @param $task_id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNbAssignmentForTask($task_id){

        $qb = $this->createQueryBuilder('a')
            ->select('COUNT(1) AS NB')
            ->where('a.task = :task_id')
            ->setParameter(':task_id', $task_id)
            ->getQuery();

        return $qb->getSingleScalarResult();
    }

    /**
     * get nb done (status > 0) assignments attached to task
     * @param $task_id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNbDoneAssignmentForTask($task_id){

        $qb = $this->createQueryBuilder('a')
            ->select('COUNT(1) AS NB')
            ->where('a.task = :task_id')
            ->andWhere('a.status > 0')
            ->setParameter(':task_id', $task_id)
            ->getQuery();

        return $qb->getSingleScalarResult();
    }

    /**
     * get nb validated (status > 1) assignments attached to task
     * @param $task_id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNbValidatedAssignmentForTask($task_id){

        $qb = $this->createQueryBuilder('a')
            ->select('COUNT(1) AS NB')
            ->where('a.task = :task_id')
            ->andWhere('a.status > 1')
            ->setParameter(':task_id', $task_id)
            ->getQuery();

        return $qb->getSingleScalarResult();
    }








    /**
     * check if the task $task_id is already assigned to user $user_id
     * @param $task_id
     * @param $user_id
     * @return bool
     */
    public function isAlreadyAssigned($task_id, $user_id){

        $qb = $this->createQueryBuilder('a')
            ->select('count(a.id) AS cpt')
            ->andWhere('a.task = :task_id')
            ->andWhere('a.assigned_to = :user_id')
            ->setParameter('task_id', $task_id)
            ->setParameter('user_id', $user_id)
            ->getQuery();

        return $qb->getScalarResult()[0]['cpt'] != 0;
    }





}
