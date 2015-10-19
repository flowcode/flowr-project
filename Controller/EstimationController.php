<?php

namespace Flower\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flower\ModelBundle\Entity\Estimation;
use Flower\CoreBundle\Form\Type\EstimationType;
use Flower\ModelBundle\Entity\EstimationItem;

/**
 * Estimation controller.
 *
 * @Route("/estimation")
 */
class EstimationController extends Controller
{
    /**
     * Lists all Estimation entities.
     *
     * @Route("/", name="estimation")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:Estimation')->createQueryBuilder('e');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);
        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a Estimation entity.
     *
     * @Route("/{id}/show", name="estimation_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Estimation $estimation)
    {
        $deleteForm = $this->createDeleteForm($estimation->getId(), 'estimation_delete');

        $estimationService = $this->get("flower.estimation");

        $processedData = $estimationService->getProcessedData($estimation);

        return array(
            'estimation' => $estimation,
            'data' => $processedData,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Finds and displays a Estimation entity.
     *
     * @Route("/{id}/copy", name="estimation_copy", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function copyAction(Estimation $estimation)
    {
        $estimationCp = new Estimation();

        $estimationCp->setName('Copy of ' . $estimation->getName());
        $estimationCp->setAccount($estimation->getAccount());
        $estimationCp->setProject($estimation->getProject());
        $estimationCp->setRatioAdmin($estimation->getRatioAdmin());
        $estimationCp->setRatioTesting($estimation->getRatioTesting());
        $estimationCp->setDailyWorkingHours($estimation->getDailyWorkingHours());

        /* persist copy */
        $em = $this->getDoctrine()->getManager();
        $em->persist($estimationCp);
        $em->flush();

        foreach ($estimation->getItems() as $item) {
            $itemCp = new EstimationItem();
            $itemCp->setName($item->getName());
            $itemCp->setDescription($item->getDescription());
            $itemCp->setOptimistic($item->getOptimistic());
            $itemCp->setPesimistic($item->getPesimistic());
            $itemCp->setEstimation($estimationCp);
            $em->persist($itemCp);
        }

        $em->flush();

        return $this->redirect($this->generateUrl('estimation_show', array('id' => $estimationCp->getId())));
    }


    /**
     * Displays a form to create a new Estimation entity.
     *
     * @Route("/new", name="estimation_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $estimation = new Estimation();
        $form = $this->createForm(new EstimationType(), $estimation);

        return array(
            'estimation' => $estimation,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Estimation entity.
     *
     * @Route("/create", name="estimation_create")
     * @Method("POST")
     * @Template("FlowerCoreBundle:Estimation:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $estimation = new Estimation();
        $form = $this->createForm(new EstimationType(), $estimation);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($estimation);
            $em->flush();

            return $this->redirect($this->generateUrl('estimation_show', array('id' => $estimation->getId())));
        }

        return array(
            'estimation' => $estimation,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Estimation entity.
     *
     * @Route("/{id}/edit", name="estimation_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(Estimation $estimation)
    {
        $editForm = $this->createForm(new EstimationType(), $estimation, array(
            'action' => $this->generateUrl('estimation_update', array('id' => $estimation->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($estimation->getId(), 'estimation_delete');

        return array(
            'estimation' => $estimation,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Estimation entity.
     *
     * @Route("/{id}/update", name="estimation_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerCoreBundle:Estimation:edit.html.twig")
     */
    public function updateAction(Estimation $estimation, Request $request)
    {
        $editForm = $this->createForm(new EstimationType(), $estimation, array(
            'action' => $this->generateUrl('estimation_update', array('id' => $estimation->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('estimation_show', array('id' => $estimation->getId())));
        }
        $deleteForm = $this->createDeleteForm($estimation->getId(), 'estimation_delete');

        return array(
            'estimation' => $estimation,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Estimation entity.
     *
     * @Route("/{id}/delete", name="estimation_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Estimation $estimation, Request $request)
    {
        $form = $this->createDeleteForm($estimation->getId(), 'estimation_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($estimation);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('estimation'));
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
