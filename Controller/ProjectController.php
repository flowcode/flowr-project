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
 * Project controller.
 *
 * @Route("/project")
 */
class ProjectController extends Controller
{

    /**
     * Lists all Project entities.
     *
     * @Route("/dashboard", name="project_dashboard")
     * @Method("GET")
     * @Template()
     */
    public function dashboardAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $projStatuses = $em->getRepository('FlowerModelBundle:Project\ProjectStatus')->findAll();

        $projectSrv = $this->get('flower.project');

        $projects = array();

        foreach ($projStatuses as $projStatus) {
            $status = array();
            $status["entity"] = $projStatus;
            $status["projects"] = $projectSrv->findByStatus($projStatus);
            $projects[] = $status;
        }

        return array(
            'projects' => $projects,
        );
    }


    /**
     * Lists all Project entities.
     *
     * @Route("/{id}/tasks/{id_task}/show", name="project_task_show")
     * @Method("GET")
     * @Template()
     */
    public function taskShowAction(Project $project, Task $task, Request $request)
    {
        $deleteForm = $this->createDeleteForm($task->getId(), 'task_delete');
        return array(
            'project' => $project,
            'task' => $task,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Lists all Project entities.
     *
     * @Route("/", name="project")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $qb = $em->getRepository('FlowerModelBundle:Project\Project')->createQueryBuilder('p');
        $this->addQueryBuilderSort($qb, 'project');

        $user = $this->getUser();
        $orgPositionSrv = $this->container->get('user.service.orgposition');
        $orgPositionSrv->getLowerPositionUsers($user);
        $qb->join("p.members", "m", "with", "1=1");
        $qb->andWhere("( p.assignee IN (:users) OR m.user IN (:members))")
            ->setParameter('users', $orgPositionSrv->getLowerPositionUsers($user, true))
            ->setParameter(":members", $orgPositionSrv->getLowerPositionUsers($user, true));

        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);

        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a Project entity.
     *
     * @Route("/{id}/show", name="project_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Project $project)
    {
        $deleteForm = $this->createDeleteForm($project->getId(), 'project_delete');
        $em = $this->getDoctrine()->getManager();
        $overallSpent = $em->getRepository('FlowerModelBundle:Project\Project')->getOverallBy($project);
        $monthSpent = $em->getRepository('FlowerModelBundle:Project\Project')->getMonthBy($project);
        $weekSpent = $em->getRepository('FlowerModelBundle:Project\Project')->getWeekBy($project);
        if (!is_null($project->getEstimated())) {
            $spentPercentage = ($overallSpent * 100) / $project->getEstimated();
        } else {
            $spentPercentage = 0;
        }
        $editForm = $this->createForm($this->get('form.type.project'), $project, array(
            'action' => $this->generateUrl('project_update', array('id' => $project->getid())),
            'method' => 'PUT',
        ));
        $spentPercentage = round($spentPercentage, 2);

        /* next events */
        $nextEvents = $em->getRepository('FlowerModelBundle:Planner\Event')->findBy(array("project" => $project), array("startDate" => "ASC"), 5);

        /* iterations */
        $iterations = $em->getRepository('FlowerModelBundle:Project\ProjectIteration')->findWithStats($project->getId());

        return array(
            'edit_form' => $editForm->createView(),
            'project' => $project,
            'overallSpent' => $overallSpent,
            'monthSpent' => $monthSpent,
            'weekSpent' => $weekSpent,
            'overallSpentRatio' => $spentPercentage,
            'nextEvents' => $nextEvents,
            'iterations' => $iterations,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Project entity.
     *
     * @Route("/new", name="project_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $project = new Project();
        $form = $this->createForm($this->get('form.type.project'), $project);

        return array(
            'project' => $project,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Project entity.
     *
     * @Route("/create", name="project_create")
     * @Method("POST")
     * @Template("FlowerProjectBundle:Project:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $project = new Project();
        $form = $this->createForm($this->get('form.type.project'), $project);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();

            $this->get('board.service.history')->addSimpleUserActivity(History::TYPE_PROJECT, $this->getUser(), $project, History::CRUD_CREATE);

            return $this->redirect($this->generateUrl('project_show', array('id' => $project->getId())));
        }

        return array(
            'project' => $project,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Project entity.
     *
     * @Route("/{id}/edit", name="project_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(Project $project)
    {
        $editForm = $this->createForm($this->get('form.type.project'), $project, array(
            'action' => $this->generateUrl('project_update', array('id' => $project->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($project->getId(), 'project_delete');

        return array(
            'project' => $project,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Project entity.
     *
     * @Route("/{id}/update", name="project_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerProjectBundle:Project:edit.html.twig")
     */
    public function updateAction(Project $project, Request $request)
    {
        $editForm = $this->createForm($this->get('form.type.project'), $project, array(
            'action' => $this->generateUrl('project_update', array('id' => $project->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->get('board.service.history')->addSimpleUserActivity(History::TYPE_PROJECT, $this->getUser(), $project, History::CRUD_UPDATE);

            return $this->redirect($this->generateUrl('project_show', array('id' => $project->getId())));
        }
        $deleteForm = $this->createDeleteForm($project->getId(), 'project_delete');

        return array(
            'project' => $project,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Deletes a Project entity.
     *
     * @Route("/{id}/delete", name="project_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Project $project, Request $request)
    {
        $form = $this->createDeleteForm($project->getId(), 'project_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($project);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('project'));
    }

    /**
     * Create Delete form
     *
     * @param integer $id
     * @param string $route
     * @return Form
     */
    protected function createDeleteForm($id, $route)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
            ->setAction($this->generateUrl($route, array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="project_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('project', $field, $type);

        return $this->redirect($this->generateUrl('project'));
    }

    /**
     * @param string $name session name
     * @param string $field field name
     * @param string $type sort type ("ASC"/"DESC")
     */
    protected function setOrder($name, $field, $type = 'ASC')
    {
        $this->getRequest()->getSession()->set('sort.' . $name, array('field' => $field, 'type' => $type));
    }

    /**
     * @param  string $name
     * @return array
     */
    protected function getOrder($name)
    {
        $session = $this->getRequest()->getSession();

        return $session->has('sort.' . $name) ? $session->get('sort.' . $name) : null;
    }

    /**
     * @param QueryBuilder $qb
     * @param string $name
     */
    protected function addQueryBuilderSort(QueryBuilder $qb, $name)
    {
        $alias = current($qb->getDQLPart('from'))->getAlias();
        if (is_array($order = $this->getOrder($name))) {
            $qb->orderBy($alias . '.' . $order['field'], $order['type']);
        }
    }


    /**
     * Displays a form to create a new Board entity.
     *
     * @Route("/iteration/{id}/board/new", name="board_new_to_project_iteration")
     * @Method("GET")
     * @Template("FlowerBoardBundle:Board:new.html.twig")
     */
    public function newToProjectIterationAction(ProjectIteration $projectIteration)
    {
        $board = new Board();
        $filter = "project_iteration_id=" . $projectIteration->getId();
        $board->setFilter($filter);

        $form = $this->createForm($this->get("form.type.board"), $board, array(
            'action' => $this->generateUrl('board_new_to_project_iteration_create', array("id" => $projectIteration->getId())),
            'method' => 'POST',
        ));
        return array(
            'board' => $board,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Board entity.
     *
     * @Route("/iteration/{id}/board/create", name="board_new_to_project_iteration_create")
     * @Method("POST")
     * @Template("FlowerBoardBundle:Board:new.html.twig")
     */
    public function createIterationBoardAction(Request $request, ProjectIteration $projectIteration)
    {
        $board = new Board();
        $filter = "project_iteration_id=" . $projectIteration->getId();
        $board->setFilter($filter);
        $board->setProjectIteration($projectIteration);

        $form = $this->createForm($this->get("form.type.board"), $board);
        if ($form->handleRequest($request)->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($board);
            $em->flush();

            return $this->redirect($this->generateUrl('board_show', array('id' => $board->getId())));
        }

        return array(
            'board' => $board,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Board entity.
     *
     * @Route("/{id}/board/new", name="board_new_to_project")
     * @Method("GET")
     * @Template("FlowerBoardBundle:Board:new.html.twig")
     */
    public function newToProjectAction(Project $project)
    {
        $board = new Board();
        $board->setFilter("project_id=" . $project->getId());

        $form = $this->createForm($this->get("form.type.board"), $board, array(
            'action' => $this->generateUrl('board_new_to_project_create', array("id" => $project->getId())),
            'method' => 'POST',
        ));
        return array(
            'board' => $board,
            'form' => $form->createView(),
        );
    }

    /**
     * new iteration.
     *
     * @Route("/{id}/iteration/new", name="project_iteration_new")
     * @Method("GET")
     * @Template()
     */
    public function newIterationAction(Project $project)
    {
        $iteration = new ProjectIteration();
        $iteration->setProject($project);

        $form = $this->createForm($this->get("form.type.project_iteration"), $iteration, array(
            'action' => $this->generateUrl('project_iteration_create', array("id" => $project->getId())),
            'method' => 'POST',
        ));

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * Edit iteration.
     *
     * @Route("/iteration/{id}/edit", name="project_iteration_edit")
     * @Method("GET")
     * @Template("FlowerProjectBundle:Project:editIteration.html.twig")
     */
    public function iterationEditAction(ProjectIteration $projectIteration)
    {

        $form = $this->createForm($this->get("form.type.project_iteration"), $projectIteration, array(
            'action' => $this->generateUrl('project_iteration_update', array("id" => $projectIteration->getId())),
            'method' => 'POST',
        ));

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * new iteration.
     *
     * @Route("/iteration/{id}", name="project_iteration_show")
     * @Method("GET")
     * @Template()
     */
    public function showIterationAction(ProjectIteration $iteration)
    {
        $em = $this->getDoctrine()->getManager();
        $burndown = array();

        $iterationPeriod = new \DatePeriod(
            $iteration->getStartDate(),
            new \DateInterval('P1D'),
            $iteration->getDueDate()->modify('+1 day')
        );

        $totalEstimated = 0;
        foreach ($iteration->getTasks() as $task) {
            $totalEstimated += $task->getEstimated();
        }

        $dataArr = array();
        $burndownPeriod = array();
        foreach ($iterationPeriod as $iterationDate) {
            $insumed = $em->getRepository('FlowerModelBundle:Board\Task')->getEstimatedOn($iteration->getId(), $iterationDate);
            $insumed = is_null($insumed) ? 0 : $insumed;

            $dataArr[] = $totalEstimated - $insumed;
            $burndownPeriod[] = $iterationDate->format('d/m/Y');
        }
        $burndown = array(
            "labe" => "Work",
            "fillColor" => "rgba(60,141,188,0.9)",
            "strokeColor" => "rgba(60,141,188,0.8)",
            "pointColor" => "#3b8bba",
            "pointStrokeColor" => "rgba(60,141,188,1)",
            "pointHighlightFill" => "#fff",
            "pointHighlightStroke" => "rgba(60,141,188,1)",
            "data" => $dataArr,
        );

        return array(
            'totalEstimated' => $totalEstimated,
            'burndownPeriod' => $burndownPeriod,
            'burndown' => $burndown,
            'iteration' => $iteration,
        );
    }

    /**
     * Creates a new Board entity.
     *
     * @Route("/{id}/iteration/create", name="project_iteration_create")
     * @Method("POST")
     * @Template("FlowerBoardBundle:Board:new.html.twig")
     */
    public function createIterationAction(Project $project, Request $request)
    {
        $iteration = new ProjectIteration();
        $iteration->setProject($project);

        $form = $this->createForm($this->get("form.type.project_iteration"), $iteration);
        if ($form->handleRequest($request)->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($iteration);
            $em->flush();

            $defaultBoard = new TaskFilter();
            $defaultBoard->setName("default");
            $defaultBoard->setProjectIteration($iteration);

            /* default filter */
            $filer = "project_id=" . $iteration->getProject()->getId();
            $filer .= "|project_iteration_id=" . $iteration->getId();

            $defaultBoard->setFilter($filer);
            $em->persist($defaultBoard);
            $em->flush();

            return $this->redirect($this->generateUrl('project_show', array('id' => $project->getId())));
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Board entity.
     *
     * @Route("/{id}/iteration/update", name="project_iteration_update")
     * @Method("POST")
     * @Template("FlowerProjectBundle:Project:editIteration.html.twig")
     */
    public function iterationUpdateAction(Request $request, ProjectIteration $projectIteration)
    {

        $form = $this->createForm($this->get("form.type.project_iteration"), $projectIteration);
        if ($form->handleRequest($request)->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirect($this->generateUrl('project_iteration_show', array('id' => $projectIteration->getId())));
        }

        return array(
            'form' => $form->createView(),
        );
    }


    /**
     * Creates a new Board entity.
     *
     * @Route("/{id}/board/create", name="board_new_to_project_create")
     * @Method("POST")
     * @Template("FlowerBoardBundle:Board:new.html.twig")
     */
    public function createBoardAction(Project $project, Request $request)
    {
        $board = new Board();
        $board->setFilter("project_id=" . $project->getId());

        $form = $this->createForm($this->get("form.type.board"), $board);
        if ($form->handleRequest($request)->isValid()) {
            $project->addBoard($board);
            $em = $this->getDoctrine()->getManager();
            $em->persist($board);
            $em->flush();

            return $this->redirect($this->generateUrl('board_show', array('id' => $board->getId())));
        }

        return array(
            'board' => $board,
            'form' => $form->createView(),
        );
    }

    /**
     * Add member.
     *
     * @Route("/{id}/members", name="project_members_full")
     * @Method("GET")
     * @Template("FlowerProjectBundle:Project:members.html.twig")
     */
    public function membersAction(Request $request, Project $project)
    {

        return array(
            'project' => $project,
        );
    }

    /**
     * Add member.
     *
     * @Route("/{id}/addmember", name="project_add_member")
     * @Method("GET")
     * @Template("FlowerProjectBundle:Project:addmember.html.twig")
     */
    public function addMemberAction(Request $request, Project $project)
    {
        $projectMembership = new ProjectMembership();
        $projectMembership->setProject($project);

        $form = $this->createForm(new ProjectMembershipType(), $projectMembership, array(
            'action' => $this->generateUrl('project_add_member_save', array("id" => $project->getId())),
            'method' => 'POST',
        ));


        return array(
            'projectMembership' => $projectMembership,
            'form' => $form->createView(),
        );
    }

    /**
     * Add member.
     *
     * @Route("/{id}/addmember", name="project_add_member_save")
     * @Method("POST")
     * @Template("FlowerProjectBundle:Project:addmember.html.twig")
     */
    public function addMemberSaveAction(Request $request, Project $project)
    {
        $projectMembership = new ProjectMembership();
        $projectMembership->setProject($project);

        $form = $this->createForm(new ProjectMembershipType(), $projectMembership);

        if ($form->handleRequest($request)->isValid()) {

            $project->addProjectMembership($projectMembership);

            $em = $this->getDoctrine()->getManager();
            $em->persist($projectMembership);
            $em->flush();

            return $this->redirect($this->generateUrl('project_show', array('id' => $project->getId())));
        }

        return array(
            'projectMembership' => $projectMembership,
            'form' => $form->createView(),
        );
    }

    /**
     * Lists all Board entities.
     *
     * @Route("/iteration/{id}/default_iew", name="project_iteration_default_view")
     * @Method("GET")
     * @Template()
     */
    public function defaultViewAction(ProjectIteration $projectIteration)
    {

        $project = $projectIteration->getProject();

        $em = $this->getDoctrine()->getManager();
        $filters = $em->getRepository('FlowerModelBundle:Board\TaskFilter')->findBy(array("projectIteration" => $projectIteration->getId()));
        $taskFilterId = null;
        if ($filters[0]) {
            $taskFilterId = $filters[0]->getId();
        }

        return $this->redirect($this->generateUrl('project_board_task_kanban', array(
            'project_id' => $project->getId(),
            'task_filter_id' => $taskFilterId,
        )));
    }

    /**
     * Lists all Board entities.
     *
     * @Route("/{project_id}/filter/{task_filter_id}/kanban", name="project_board_task_kanban")
     * @Method("GET")
     * @Template()
     */
    public function tasksKanbanAction($project_id, $task_filter_id)
    {
        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('FlowerModelBundle:Project\Project')->find($project_id);
        $taskFilter = $em->getRepository('FlowerModelBundle:Board\TaskFilter')->find($task_filter_id);

        $filters = array();
        if ($taskFilter->getProjectIteration()) {
            $projectIteration = $taskFilter->getProjectIteration();
            $filters = $em->getRepository('FlowerModelBundle:Board\TaskFilter')->findBy(array("projectIteration" => $projectIteration->getId()));
        }

        return array(
            'filters' => $filters,
            'project' => $project,
            'filter' => $taskFilter,
        );
    }

    /**
     * Lists all Board entities.
     *
     * @Route("/{project_id}/filter/{task_filter_id}/list", name="project_board_task_list")
     * @Method("GET")
     * @Template()
     */
    public function tasksListAction(Request $request, $project_id, $task_filter_id)
    {

        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:Board\Task')->createQueryBuilder('t');
        $qb->join("t.status", "s");

        $taskFilter = $em->getRepository('FlowerModelBundle:Board\TaskFilter')->find($task_filter_id);
        $filter = $this->get("board.service.task")->getTaskFilter($taskFilter->getFilter());
        $qb = $taskRepo = $em->getRepository('FlowerModelBundle:Board\Task')->findByStatusQB(null, $filter);

        $paginator = $this->get('knp_paginator')->paginate($qb, $request->get('page', 1), 20);

        $statuses = $em->getRepository('FlowerModelBundle:Board\TaskStatus')->findAll();
        $users = $em->getRepository('FlowerModelBundle:User\User')->findAll();

        $project = $em->getRepository('FlowerModelBundle:Project\Project')->find($project_id);

        return array(
            'project' => $project,
            'assigneeFilter' => null,
            'statusFilter' => null,
            'users' => $users,
            'statuses' => $statuses,
            'filter' => $taskFilter,
            'paginator' => $paginator,
        );
    }

}
