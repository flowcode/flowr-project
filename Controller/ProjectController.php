<?php

namespace Flower\CoreBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Flower\CoreBundle\Form\Type\ProjectType;
use Flower\CoreBundle\Form\Type\TaskProjectType;
use Flower\CoreBundle\Form\Type\TaskType;
use Flower\ModelBundle\Entity\Project;
use Flower\ModelBundle\Entity\TaskStatus;
use Flower\ModelBundle\Entity\User\User;
use Flower\ModelBundle\Entity\TaskType as TaskType2;
use Flower\ModelBundle\Entity\Task;
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
        $userAccount = $this->getUser()->getUserAccount();

        $projStatuses = $em->getRepository('FlowerModelBundle:ProjectStatus')->findAll();
        $projectRepo = $em->getRepository("FlowerModelBundle:Project");

        $projects = array();

        foreach ($projStatuses as $projStatus) {
            $status = array();
            $status["entity"] = $projStatus;
            $status["projects"] = $projectRepo->findByStatus($userAccount->getId(), $projStatus->getId());
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
        $userAccount = $this->getUser()->getUserAccount();
        
        $qb = $em->getRepository('FlowerModelBundle:Project')->createQueryBuilder('p');
        $qb->where("p.userAccount = :user_account")->setParameter("user_account", $userAccount);
        $this->addQueryBuilderSort($qb, 'project');
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
        $overallSpent = $em->getRepository("FlowerModelBundle:TimeLog")->getOverallBy($project->getId());
        $monthSpent = $em->getRepository("FlowerModelBundle:TimeLog")->getMonthBy($project->getId());
        $weekSpent = $em->getRepository("FlowerModelBundle:TimeLog")->getWeekBy($project->getId());

        $projectBoards = $em->getRepository("FlowerModelBundle:Board")->getCurrentBoards(null, $project->getId());

        if (!is_null($project->getEstimated())) {
            $spentPercentage = ($overallSpent * 100) / $project->getEstimated();
        } else {
            $spentPercentage = 0;
        }

        $spentPercentage = round($spentPercentage, 2);

        return array(
            'project' => $project,
            'overallSpent' => $overallSpent,
            'monthSpent' => $monthSpent,
            'weekSpent' => $weekSpent,
            'projectBoards' => $projectBoards,
            'overallSpentRatio' => $spentPercentage,
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
     * @Template("FlowerCoreBundle:Project:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $project = new Project();
        $form = $this->createForm($this->get('form.type.project'), $project);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $userAccount = $this->getUser()->getUserAccount();
            $project->setUserAccount($userAccount);

            $em->persist($project);
            $em->flush();

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
     * @Template("FlowerCoreBundle:Project:edit.html.twig")
     */
    public function updateAction(Project $project, Request $request)
    {
        $editForm = $this->createForm($this->get('form.type.project'), $project, array(
            'action' => $this->generateUrl('project_update', array('id' => $project->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

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
     * @param integer                       $id
     * @param string                        $route
     * @return Form
     */
    protected function createDeleteForm($id, $route)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
                        ->setAction($this->generateUrl($route, array('id' => $id)))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
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
     * @param string $name  session name
     * @param string $field field name
     * @param string $type  sort type ("ASC"/"DESC")
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
     * @param string       $name
     */
    protected function addQueryBuilderSort(QueryBuilder $qb, $name)
    {
        $alias = current($qb->getDQLPart('from'))->getAlias();
        if (is_array($order = $this->getOrder($name))) {
            $qb->orderBy($alias . '.' . $order['field'], $order['type']);
        }
    }
}
