<?php

namespace Flower\ProjectBundle\Service;


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
    
    public function getBoardsWithStadistics($project){
		$em = $this->getEntityManager();
		$stadisticsByStatus = $em->getRepository('FlowerModelBundle:Project\Project')->getCompleteBoardsByProjectAndStatus($project, array(TaskStatus::STATUS_BACKLOG,TaskStatus::STATUS_TODO,TaskStatus::STATUS_DOING));
		$allStadistics = $em->getRepository('FlowerModelBundle:Project\Project')->getCompleteBoardsByProject($project);		
		$oldBoardId = "";
		$oldStatusId = "";
		foreach ($allStadistics as &$board) {
			$board["pendding_time"] = 0;
			$board["pendding_countTasks"] = 0;
			foreach ($stadisticsByStatus as $boardStatus) {
				if($board["board_id"] == $boardStatus["board_id"]){
					$board["pendding_time"] = $boardStatus["time"];
					$board["pendding_countTasks"] = $boardStatus["countTasks"];
				}
			}	
		}

		return $allStadistics;
    }
    /**
    * Get entityManager
    * @return  
    */
    public function getEntityManager()
    {
        return $this->entityManager;
    }
    
    /**
    * Set entityManager
    * @return  
    */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }
}
