<?php

namespace Flower\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flower\ModelBundle\Entity\EstimationItem;
use Flower\CoreBundle\Form\Type\EstimationItemType;
use Flower\ModelBundle\Entity\Estimation;

/**
 * EstimationItem controller.
 *
 * @Route("/estimationitem")
 */
class EstimationItemController extends Controller
{
    /**
     * Lists all EstimationItem entities.
     *
     * @Route("/", name="estimationitem")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:EstimationItem')->createQueryBuilder('e');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);
        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a EstimationItem entity.
     *
     * @Route("/{id}/show", name="estimationitem_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(EstimationItem $estimationitem)
    {
        $deleteForm = $this->createDeleteForm($estimationitem->getId(), 'estimationitem_delete');

        return array(
            'estimationitem' => $estimationitem,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new EstimationItem entity.
     *
     * @Route("/new", name="estimationitem_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $estimationitem = new EstimationItem();
        $form = $this->createForm(new EstimationItemType(), $estimationitem);

        return array(
            'estimationitem' => $estimationitem,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new EstimationItem entity.
     *
     * @Route("/estimation/{id}/new", name="estimationitem_new_toestimation")
     * @Method("GET")
     * @Template("FlowerCoreBundle:EstimationItem:new.html.twig")
     */
    public function newToEstimationAction(Estimation $estimation)
    {
        $estimationitem = new EstimationItem();
        $estimationitem->setEstimation($estimation);

        $form = $this->createForm(new EstimationItemType(), $estimationitem);

        return array(
            'estimationitem' => $estimationitem,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new EstimationItem entity.
     *
     * @Route("/create", name="estimationitem_create")
     * @Method("POST")
     * @Template("FlowerCoreBundle:EstimationItem:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $estimationitem = new EstimationItem();
        $form = $this->createForm(new EstimationItemType(), $estimationitem);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($estimationitem);
            $em->flush();
            if(!is_null($estimationitem->getEstimation())){
                return $this->redirect($this->generateUrl('estimation_show', array('id' => $estimationitem->getEstimation()->getId())));
            }
            return $this->redirect($this->generateUrl('estimationitem_show', array('id' => $estimationitem->getId())));
        }

        return array(
            'estimationitem' => $estimationitem,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing EstimationItem entity.
     *
     * @Route("/{id}/edit", name="estimationitem_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(EstimationItem $estimationitem)
    {
        $editForm = $this->createForm(new EstimationItemType(), $estimationitem, array(
            'action' => $this->generateUrl('estimationitem_update', array('id' => $estimationitem->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($estimationitem->getId(), 'estimationitem_delete');

        return array(
            'estimationitem' => $estimationitem,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing EstimationItem entity.
     *
     * @Route("/{id}/update", name="estimationitem_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerCoreBundle:EstimationItem:edit.html.twig")
     */
    public function updateAction(EstimationItem $estimationitem, Request $request)
    {
        $editForm = $this->createForm(new EstimationItemType(), $estimationitem, array(
            'action' => $this->generateUrl('estimationitem_update', array('id' => $estimationitem->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            if(!is_null($estimationitem->getEstimation())){
                return $this->redirect($this->generateUrl('estimation_show', array('id' => $estimationitem->getEstimation()->getId())));
            }
            return $this->redirect($this->generateUrl('estimationitem_show', array('id' => $estimationitem->getId())));
        }
        $deleteForm = $this->createDeleteForm($estimationitem->getId(), 'estimationitem_delete');

        return array(
            'estimationitem' => $estimationitem,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a EstimationItem entity.
     *
     * @Route("/{id}/delete", name="estimationitem_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(EstimationItem $estimationitem, Request $request)
    {
        $form = $this->createDeleteForm($estimationitem->getId(), 'estimationitem_delete');

        $idEstima = null;
        if(!is_null($estimationitem->getEstimation())){
            $idEstima = $estimationitem->getEstimation()->getId();
        }
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($estimationitem);
            $em->flush();

            if(!is_null($idEstima)){
                return $this->redirect($this->generateUrl('estimation_show', array('id' => $idEstima)));
            }
        }

        return $this->redirect($this->generateUrl('estimationitem'));
    }

    /**
     * Create Delete form
     *
     * @param integer                       $id
     * @param string                        $route
     * @return \Symfony\Component\Form\Form
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
