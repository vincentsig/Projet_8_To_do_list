<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    private SessionInterface $session;

    public function __construct(ManagerRegistry $registry, SessionInterface $session)
    {
        $this->session = $session;
        parent::__construct($registry, Task::class);
    }

    /**
     * deleteTask
     *
     * @param  Task $task
     * @return void
     */
    public function deleteTask(Task $task): void
    {
        $this->_em->remove($task);
        $this->_em->flush();

        $this->session->getFlashBag()->add('success', 'La tâche a bien été supprimée.');
    }

    /**
     * toggleTask
     *
     * @param  Task $task
     * @return void
     */
    public function toggleTask(Task $task): void
    {
        $task->toggle(!$task->isDone());
        $this->_em->flush();
        $this->checkTaskStatus($task);
    }

    /**
     * checkTaskStatus
     *
     * @param  Task $task
     * @return void
     */
    private function checkTaskStatus(Task $task): void
    {
        if ($task->isDone() === true) {
            $this->session->getFlashBag()->add(
                'success',
                sprintf(
                    'La tâche %s a bien été marquée comme faite.',
                    $task->getTitle()
                )
            );
            return;
        }
        $this->session->getFlashBag()->add(
            'success',
            sprintf(
                'La tâche %s a bien été marquée comme non terminé.',
                $task->getTitle()
            )
        );
    }

    // /**
    //  * @return Task[] Returns an array of Task objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Task
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
