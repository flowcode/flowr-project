<?php

namespace Flower\ProjectBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/**
 * ProjectRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProjectRepository extends EntityRepository
{

    public function findByStatus($statusId)
    {
        $qb = $this->createQueryBuilder("p");
        $qb->where("p.status = :status_id")->setParameter("status_id", $statusId);
        $qb->andWhere("p.enabled = :enabled")->setParameter("enabled", true);
        $qb->orderBy("p.updated", "DESC");
        return $qb->getQuery()->getResult();
    }

    public function findAllActive()
    {
        $qb = $this->findAllQb();
        return $qb->getQuery()->getResult();
    }

    public function findAllQb($alias = 'p', $enabled = true)
    {
        $qb = $this->createQueryBuilder($alias);
        $qb->andWhere("p.enabled = :enabled")->setParameter("enabled", $enabled);
        $qb->orderBy("p.updated", "DESC");
        return $qb;
    }

    public function findByStatusQb($statusId, $alias = 'p')
    {
        $qb = $this->createQueryBuilder($alias);
        $qb->where("p.status = :status_id")->setParameter("status_id", $statusId);
        $qb->andWhere("p.enabled = :enabled")->setParameter("enabled", true);
        $qb->orderBy("p.updated", "DESC");
        return $qb;
    }

    public function getCountByStatus($userAccountId, $statusId)
    {
        $qb = $this->createQueryBuilder("p");
        $qb->select("COUNT(p)");
        $qb->where("p.status = :status_id")->setParameter("status_id", $statusId);
        $qb->where("p.enabled = :enabled")->setParameter("enabled", true);
        $qb->andWhere("p.userAccount = :userAccount")->setParameter("userAccount", $userAccountId);
        $qb->orderBy("p.updated", "DESC");
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getCountEnabled()
    {
        $qb = $this->createQueryBuilder("p");
        $qb->select("COUNT(p)");
        $qb->where("p.enabled = :enabled")->setParameter("enabled", true);
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function search($completeText, $texts, $limit = 10)
    {
        $qb = $this->createQueryBuilder("p");
        $qb->andWhere("p.name like :text")->setParameter("text", "%" . $completeText . "%");
        $qb->setMaxResults($limit);
        $result = $qb->getQuery()->getResult();


        $qb = $this->createQueryBuilder("p");
        $qb->andWhere("p.description like :text")->setParameter("text", "%" . $completeText . "%");
        $qb->setMaxResults($limit);
        $result = array_merge($result, $qb->getQuery()->getResult());

        $qb = $this->createQueryBuilder("p");
        $count = 0;
        foreach ($texts as $text) {
            $qb->orWhere("p.name like :text")->setParameter("text", "%" . $text . "%");
            $qb->orWhere("p.description like :text")->setParameter("text", "%" . $text . "%");
            $qb->setMaxResults($limit);
            $count++;
        }
        $result = array_merge($result, $qb->getQuery()->getResult());

        return array_unique($result, SORT_REGULAR);
    }


    public function getOverallBy($project)
    {
        $qb = $this->createQueryBuilder("p");
        $qb->select("SUM(tl.hours)");
        $qb->join("p.tasks", "t");
        $qb->join(
            'FlowerModelBundle:Board\TimeLog',
            'tl',
            Join::WITH,
            'tl.task = t.id'
        );
        $qb->where("p.id = :project_id")->setParameter("project_id", $project);
        $query = $qb->getQuery();
        return $query->getSingleScalarResult();
    }

    public function getMonthBy($project)
    {
        $qb = $this->createQueryBuilder("p");
        $qb->select("SUM(tl.hours)");
        $qb->join("p.tasks", "t");
        $qb->join(
            'FlowerModelBundle:Board\TimeLog',
            'tl',
            Join::WITH,
            'tl.task = t.id'
        );
        $qb->where('MONTH(tl.spentOn) = :month')->setParameter('month', date('m'));
        $qb->andWhere("p.id = :project")->setParameter("project", $project);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getWeekBy($project)
    {
        $qb = $this->createQueryBuilder("p");
        $qb->select("SUM(tl.hours)");
        $qb->join("p.tasks", "t");
        $qb->join(
            'FlowerModelBundle:Board\TimeLog',
            'tl',
            Join::WITH,
            'tl.task = t.id'
        );

        $qb->where('tl.spentOn > :first_of_week')->setParameter('first_of_week', date('Y-m-d', strtotime("last week")));
        $qb->andWhere('tl.spentOn < :last_of_week')->setParameter('last_of_week', date('Y-m-d', strtotime("next week")));
        $qb->andWhere("p.id = :project")->setParameter("project", $project);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getCompleteBoardsByProjectAndStatus($project, $status)
    {
        $qb = $this->createQueryBuilder("p");
        $qb->select("p.id as projectId, b.id as board_id,b.name as board_name, SUM(tl.hours) as time, count(t) as countTasks");
        $qb->join("p.boards", "b");
        $qb->leftJoin("b.tasks", "t");
        $qb->leftJoin("t.status", "ts");
        $qb->leftJoin(
            'FlowerModelBundle:Board\TimeLog',
            'tl',
            Join::WITH,
            'tl.task = t.id'
        );
        $qb->andWhere("p.id = :project")->setParameter("project", $project);
        $qb->andWhere("t.project = :project_id")->setParameter("project_id", $project);
        $qb->andWhere("ts.name in (:status)")->setParameter("status", $status);
        $qb->groupBy("b");
        $qb->orderBy("b.id");
        return $qb->getQuery()->getResult();
    }

    public function getCompleteBoardsByProject($project)
    {
        $qb = $this->createQueryBuilder("p");
        $qb->select("p.id as projectId, b.id as board_id,b.name as board_name, SUM(tl.hours) as time, count(t) as countTasks");
        $qb->join("p.boards", "b");
        $qb->join("p.tasks", "t");
        $qb->join("t.status", "ts");
        $qb->leftJoin(
            'FlowerModelBundle:Board\TimeLog',
            'tl',
            Join::WITH,
            'tl.task = t.id'
        );
        $qb->andWhere("p.id = :project")->setParameter("project", $project);
        $qb->andWhere("t.project = :project_id")->setParameter("project_id", $project);
        $qb->groupBy("b");
        $qb->orderBy("b.id");
        return $qb->getQuery()->getResult();
    }

    public function findByBoard($board)
    {
        $qb = $this->createQueryBuilder("p");
        $qb->join("p.boards", "b");
        $qb->where("b.id = :board")->setParameter("board", $board->getId());
        $querry = $qb->getQuery();
        return $querry->getOneOrNullResult();
    }
}
