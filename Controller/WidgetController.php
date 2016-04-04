<?php

namespace Flower\ProjectBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Flower\CoreBundle\Form\Type\ProjectType;
use Flower\CoreBundle\Form\Type\TaskProjectType;
use Flower\CoreBundle\Form\Type\TaskType;
use Flower\ModelBundle\Entity\Board\History;
use Flower\ModelBundle\Entity\Board\TaskFilter;
use Flower\ModelBundle\Entity\Project\Project;
use Flower\ModelBundle\Entity\Project\ProjectIteration;
use Flower\ModelBundle\Entity\Project\ProjectMembership;
use Flower\ModelBundle\Entity\User\User;
use Flower\ModelBundle\Entity\Board\TaskStatus;
use Flower\ModelBundle\Entity\Board\TaskType as TaskType2;
use Flower\ModelBundle\Entity\Board\Task;
use Flower\ModelBundle\Entity\Board\Board;
use Flower\ProjectBundle\Form\Type\ProjectMembershipType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Project widgets controller.
 *
 * @Route("/project/widget")
 */
class WidgetController extends Controller
{

    /**
     * Lists all Project entities.
     *
     * @Route("/myprojects", name="project_widget_myprojects")
     * @Method("GET")
     * @Template()
     */
    public function myProjectsAction(Request $request)
    {
        $myProjects = $this->get('flower.project')->findMineActives();

        return array(
            'myProjects' => $myProjects,
        );
    }

}
