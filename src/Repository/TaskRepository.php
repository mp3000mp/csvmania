<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\Assignment;
use App\Form\Type\TaskType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }










    public function getDashboard($nbMaxTasks){

        $r = $this->createQueryBuilder('t')
            ->innerJoin('t.assignments', 'a')
            ->addSelect('a')
            ->innerJoin('a.assigned_to', 'u')
            ->addSelect('u')
            ->innerJoin('t.contents', 'c')
            ->addSelect('COUNT(DISTINCT c.id) AS nb_contents')
            ->innerJoin('t.fields','f')
            ->addSelect('COUNT(DISTINCT f.id) AS nb_fields')
            ->leftJoin('a.answers', 'an')
            ->addSelect('COUNT(DISTINCT an.id) AS nb_answers')
            ->groupBy('t.id')
            ->addGroupBy('a.id')
            ->orderBy('t.status', 'ASC')
            ->addOrderBy('t.status', 'ASC')
            ->setMaxResults($nbMaxTasks)
            ->getQuery()
            ->getResult();

        if(empty($r) || $r[0][0] == null){
            return [];
        }else{
            return $r;
        }


    }











    /**
     * check if field id has to be answered regarding to task settings
     * @param $task_id
     * @param $field_id
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function hasToBeAnswered($task_id, $field_id){

        $qb = $this->createQueryBuilder('t')
            ->select('COUNT(1) AS nb')
            ->innerJoin('t.fields', 'f')
            ->where('t.id = :task_id')
            ->andWhere('f.id = :field_id')
            ->setParameter(':task_id', $task_id)
            ->setParameter(':field_id', $field_id)
            ->getQuery();

        return $qb->getSingleScalarResult() == 1;

    }




    /**
     * trouve le nombre de tâche qu'il reste à piocher pour le user
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNbTodoTasks($user_id){
        $qb = $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->andWhere('t.status = 0')
            ->andWhere('t.id NOT IN (SELECT a FROM App\Entity\Assignment a WHERE a.assigned_to = :user_id)')
            ->setParameter('user_id', $user_id)
            ->getQuery();

        return $qb->getSingleScalarResult();
    }


    /**
     * trouve la file d'attente dans l'ordre de priorité
     * @param $user_id
     * @param $nb nb tasks to return
     * @return array => [ [Task,nb_contents, nb_fields] , [...] ]
     */
    public function getNextTodoTasks($user_id, $nb){

        $r = $this->createQueryBuilder('t')
            ->select('t')
            ->innerJoin('t.contents', 'c')
            ->addSelect('COUNT(DISTINCT c.id) AS nb_contents')
            ->innerJoin('t.fields', 'f')
            ->addSelect('COUNT(DISTINCT f.id) AS nb_fields')
            ->andWhere('t.status = 0')
            ->andWhere('t.id NOT IN (SELECT a FROM App\Entity\Assignment a WHERE a.assigned_to = :user_id)')
            ->setParameter('user_id', $user_id)
            ->groupBy('t.id')
            ->orderBy('t.priority', 'ASC')
            ->addOrderBy('t.deadline', 'ASC')
            ->setMaxResults($nb)
            ->getQuery()
            ->getResult();

        if(empty($r) || $r[0][0] == null){
            return [];
        }else{
            return $r;
        }

    }












    /**
     * @param $task_id
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTaskProgressionDone($task_id){

        $qb = $this->createQueryBuilder('t')
            ->innerJoin('t.assignments', 'a')
            ->select('COUNT(DISTINCT a.id) AS nb_assignments')
            ->leftJoin('a.answers', 'w')
            ->addSelect('COUNT(DISTINCT w.id) AS nb_answers')
            ->innerJoin('t.contents', 'c')
            ->addSelect('COUNT(DISTINCT c.id) AS nb_contents')
            ->innerJoin('t.fields', 'f')
            ->addSelect('COUNT(DISTINCT f.id) AS nb_fields')
            ->andWhere('t.id = :task_id')
            ->setParameter(':task_id', $task_id)
            ->groupBy('t.id')
            ->getQuery();
        return $qb->getSingleResult();

    }


    /**
     * @param $id
     * @return array
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTaskProgressionValidated($id): array{
        $qb = $this->createQueryBuilder('ta')
            ->select('count(to.id)')
            ->innerJoin('ta.tokens','to')
            ->andWhere('ta.id = :id')
            ->andWhere('to.validated_at IS NOT NULL')
            ->setParameter('id', $id)
            ->getQuery();

        return $qb->getSingleResult();
    }
}
