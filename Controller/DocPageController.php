<?php

namespace Flower\ProjectBundle\Controller;

use Flower\CoreBundle\Form\Type\DocPageType;
use Flower\ModelBundle\Entity\Project\DocPage;
use Flower\ModelBundle\Entity\Project\Project;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * DocPage controller.
 *
 * @Route("/docpage")
 */
class DocPageController extends Controller
{

    /**
     * Lists all DocPage entities.
     *
     * @Route("/", name="docpage")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:Project\DocPage')->createQueryBuilder('d');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);
        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a DocPage entity.
     *
     * @Route("/{id}/show", name="docpage_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(DocPage $docpage)
    {
        $deleteForm = $this->createDeleteForm($docpage->getId(), 'docpage_delete');
        $project = "";
        if($docpage->getProject()){
            $project = $docpage->getProject();
        }
        return array(
            'project' => $project,
            'docpage' => $docpage,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new DocPage entity.
     *
     * @Route("/new", name="docpage_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $docpage = new DocPage();
        $form = $this->createForm($this->get("form.type.docproject"), $docpage);

        return array(
            'docpage' => $docpage,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new DocPage entity.
     *
     * @Route("/project/{id}/new", name="docpage_add_toproj")
     * @Method("GET")
     * @Template("FlowerProjectBundle:DocPage:new.html.twig")
     */
    public function addToProjectAction(Project $project)
    {
        $docpage = new DocPage();
        $docpage->setProject($project);
        $form = $this->createForm($this->get("form.type.docproject"), $docpage);

        return array(
            'project' => $project,
            'docpage' => $docpage,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new DocPage entity.
     *
     * @Route("/create", name="docpage_create")
     * @Method("POST")
     * @Template("FlowerProjectBundle:DocPage:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $docpage = new DocPage();
        $form = $this->createForm($this->get("form.type.docproject"), $docpage);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($docpage);
            $em->flush();

            return $this->redirect($this->generateUrl('docpage_show', array('id' => $docpage->getId())));
        }

        return array(
            'docpage' => $docpage,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing DocPage entity.
     *
     * @Route("/{id}/edit", name="docpage_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(DocPage $docpage)
    {
        $editForm = $this->createForm($this->get("form.type.docproject"), $docpage, array(
            'action' => $this->generateUrl('docpage_update', array('id' => $docpage->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($docpage->getId(), 'docpage_delete');
        $project = "";
        if($docpage->getProject()){
            $project = $docpage->getProject();
        }
        return array(
            'project'=> $project,
            'docpage' => $docpage,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing DocPage entity.
     *
     * @Route("/{id}/update", name="docpage_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerProjectBundle:DocPage:edit.html.twig")
     */
    public function updateAction(DocPage $docpage, Request $request)
    {
        $editForm = $this->createForm($this->get("form.type.docproject"), $docpage, array(
            'action' => $this->generateUrl('docpage_update', array('id' => $docpage->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('docpage_show', array('id' => $docpage->getId())));
        }
        $deleteForm = $this->createDeleteForm($docpage->getId(), 'docpage_delete');

        return array(
            'docpage' => $docpage,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a DocPage entity.
     *
     * @Route("/{id}/delete", name="docpage_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(DocPage $docpage, Request $request)
    {
        $form = $this->createDeleteForm($docpage->getId(), 'docpage_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($docpage);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('docpage'));
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
