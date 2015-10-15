<?php

namespace Flower\CoreBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Flower\CoreBundle\Form\Type\ProjectStatusType;
use Flower\ModelBundle\Entity\ProjectStatus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * ProjectStatus controller.
 *
 * @Route("/projectstatus")
 */
class ProjectStatusController extends Controller
{

    /**
     * Lists all ProjectStatus entities.
     *
     * @Route("/", name="projectstatus")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:ProjectStatus')->createQueryBuilder('p');
        $this->addQueryBuilderSort($qb, 'projectstatus');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);

        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a ProjectStatus entity.
     *
     * @Route("/{id}/show", name="projectstatus_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(ProjectStatus $projectstatus)
    {
        $deleteForm = $this->createDeleteForm($projectstatus->getId(), 'projectstatus_delete');

        return array(
            'projectstatus' => $projectstatus,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new ProjectStatus entity.
     *
     * @Route("/new", name="projectstatus_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $projectstatus = new ProjectStatus();
        $form = $this->createForm(new ProjectStatusType(), $projectstatus);

        return array(
            'projectstatus' => $projectstatus,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new ProjectStatus entity.
     *
     * @Route("/create", name="projectstatus_create")
     * @Method("POST")
     * @Template("FlowerCoreBundle:ProjectStatus:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $projectstatus = new ProjectStatus();
        $form = $this->createForm(new ProjectStatusType(), $projectstatus);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($projectstatus);
            $em->flush();

            return $this->redirect($this->generateUrl('projectstatus_show', array('id' => $projectstatus->getId())));
        }

        return array(
            'projectstatus' => $projectstatus,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ProjectStatus entity.
     *
     * @Route("/{id}/edit", name="projectstatus_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(ProjectStatus $projectstatus)
    {
        $editForm = $this->createForm(new ProjectStatusType(), $projectstatus, array(
            'action' => $this->generateUrl('projectstatus_update', array('id' => $projectstatus->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($projectstatus->getId(), 'projectstatus_delete');

        return array(
            'projectstatus' => $projectstatus,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing ProjectStatus entity.
     *
     * @Route("/{id}/update", name="projectstatus_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerCoreBundle:ProjectStatus:edit.html.twig")
     */
    public function updateAction(ProjectStatus $projectstatus, Request $request)
    {
        $editForm = $this->createForm(new ProjectStatusType(), $projectstatus, array(
            'action' => $this->generateUrl('projectstatus_update', array('id' => $projectstatus->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('projectstatus_show', array('id' => $projectstatus->getId())));
        }
        $deleteForm = $this->createDeleteForm($projectstatus->getId(), 'projectstatus_delete');

        return array(
            'projectstatus' => $projectstatus,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="projectstatus_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('projectstatus', $field, $type);

        return $this->redirect($this->generateUrl('projectstatus'));
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

    /**
     * Deletes a ProjectStatus entity.
     *
     * @Route("/{id}/delete", name="projectstatus_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(ProjectStatus $projectstatus, Request $request)
    {
        $form = $this->createDeleteForm($projectstatus->getId(), 'projectstatus_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($projectstatus);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('projectstatus'));
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

}
