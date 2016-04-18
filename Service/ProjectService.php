<?php

namespace Flower\ProjectBundle\Service;


use Flower\ModelBundle\Entity\Project\ProjectStatus;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Flower\BoardBundle\Model\TaskStatus;

/**
 * Description of ProjectService
 *
 * @author Francisco Memoli Olmos <fmemoli@flowcode.com.ar>
 */
class ProjectService implements ContainerAwareInterface
{

    /**
     * @var Container
     */
    private $container;
    /**
     * @var Entity Managet
     */
    private $entityManager;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = NULL)
    {
        $this->container = $container;
        $this->entityManager = $this->container->get("doctrine.orm.entity_manager");
    }

    public function findAll()
    {
        $alias = 'p';
        $qb = $this->entityManager->getRepository('FlowerModelBundle:Project\Project')->findAllQb();
        $orgPositionSrv = $this->container->get('user.service.orgposition');
        $user = $this->container->get('security.context')->getToken()->getUser();

        $qb->join($alias . ".members", "m", "with", "1=1");
        $qb->andWhere("( p.assignee IN (:users) OR m.user IN (:members))")
            ->setParameter('users', $orgPositionSrv->getLowerPositionUsers($user, true))
            ->setParameter(":members", $orgPositionSrv->getLowerPositionUsers($user, true));

        return $qb->getQuery()->getResult();
    }

    public function findMineActives()
    {
        $alias = 'p';
        $qb = $this->entityManager->getRepository('FlowerModelBundle:Project\Project')->findAllQb();
        $qb->join("p.status", "ps");
        $qb->andWhere("ps.name NOT IN (:inactives)")->setParameter("inactives", array("status_finished"));
        
        $user = $this->container->get('security.context')->getToken()->getUser();

        $qb->join($alias . ".members", "m", "with", "1=1");
        $qb->andWhere("( p.assignee = :assignee OR m.user = :member)")
            ->setParameter('assignee', $user)
            ->setParameter(":member", $user);

        return $qb->getQuery()->getResult();
    }

    public function findByStatus(ProjectStatus $projectStatus)
    {
        $alias = 'p';

        $qb = $this->entityManager->getRepository('FlowerModelBundle:Project\Project')->findByStatusQb($projectStatus->getId(), $alias);

        $orgPositionSrv = $this->container->get('user.service.orgposition');
        $user = $this->container->get('security.context')->getToken()->getUser();

        $qb->join($alias . ".members", "m", "with", "1=1");
        $qb->andWhere("( p.assignee IN (:users) OR m.user IN (:members))")
            ->setParameter('users', $orgPositionSrv->getLowerPositionUsers($user, true))
            ->setParameter(":members", $orgPositionSrv->getLowerPositionUsers($user, true));

        return $qb->getQuery()->getResult();
    }
    public function findWithStats($project){
        $iterationswithStatus = $this->entityManager->getRepository('FlowerModelBundle:Project\ProjectIteration')->findWithStats($project->getId());
        $iterationsSpents = $this->entityManager->getRepository('FlowerModelBundle:Project\ProjectIteration')->findSpentByProject($project->getId());
        for ($i=0; $i < count($iterationsSpents); $i++) { 
           $iterationswithStatus[$i]["spent"] = $iterationsSpents[$i]["spent"];         
        }
        return $iterationswithStatus;
    }
    public function getBoardsWithStadistics($project)
    {
        $em = $this->getEntityManager();
        $stadisticsByStatus = $em->getRepository('FlowerModelBundle:Project\Project')->getCompleteBoardsByProjectAndStatus($project, array(TaskStatus::STATUS_BACKLOG, TaskStatus::STATUS_TODO, TaskStatus::STATUS_DOING));
        $allStadistics = $em->getRepository('FlowerModelBundle:Project\Project')->getCompleteBoardsByProject($project);
        $oldBoardId = "";
        $oldStatusId = "";
        foreach ($allStadistics as &$board) {
            $board["pendding_time"] = 0;
            $board["pendding_countTasks"] = 0;
            foreach ($stadisticsByStatus as $boardStatus) {
                if ($board["board_id"] == $boardStatus["board_id"]) {
                    $board["pendding_time"] = $boardStatus["time"];
                    $board["pendding_countTasks"] = $boardStatus["countTasks"];
                }
            }
        }

        return $allStadistics;
    }

    /**
     * Get entityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * Set entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }
}
