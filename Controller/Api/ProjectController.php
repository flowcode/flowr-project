<?php

namespace Flower\ProjectBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as FOSView;
use Symfony\Component\HttpFoundation\Request;
use Flower\ModelBundle\Entity\Project\Project;

/**
 * Project controller.
 */
class ProjectController extends FOSRestController
{
    public function getAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $projects = $em->getRepository('FlowerModelBundle:Project\Project')->findAll();

        $view = FOSView::create($projects, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('public_api'));
        return $this->handleView($view);
    }

    public function getByIdAction(Request $request, Project $project)
    {
        $em = $this->getDoctrine()->getManager();

        $overallSpent = $em->getRepository("FlowerModelBundle:Project\Project")->getOverallBy($project);
        $monthSpent = $em->getRepository("FlowerModelBundle:Project\Project")->getMonthBy($project);
        $weekSpent = $em->getRepository("FlowerModelBundle:Project\Project")->getWeekBy($project);

        if (!is_null($project->getEstimated())) {
            $spentPercentage = ($overallSpent * 100) / $project->getEstimated();
        } else {
            $spentPercentage = 0;
        }

        $spentPercentage = round($spentPercentage, 2);

        $reponse = array(
            'project' => $project,
            'overallSpent' => $overallSpent,
            'monthSpent' => $monthSpent,
            'weekSpent' => $weekSpent,
            'projectBoards' => $project->getBoards(),
            'overallSpentRatio' => $spentPercentage,
        );

        $view = FOSView::create($reponse, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('public_api'));
        return $this->handleView($view);
    }
}
