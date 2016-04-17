<?php

namespace Flower\ProjectBundle\Controller;

use Flower\CoreBundle\Form\Type\DocPageType;
use Flower\ModelBundle\Entity\Project\DocPage;
use Flower\ModelBundle\Entity\Project\Project;
use Flower\ModelBundle\Entity\Project\ProjectMembership;
use Flower\ProjectBundle\Form\Type\ProjectMembershipType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * DocPage controller.
 *
 * @Route("/project-members")
 */
class ProjectMembershipController extends Controller
{

    /**
     * Lists all DocPage entities.
     *
     * @Route("/project/{id}", name="project_members_full")
     * @Method("GET")
     * @Template()
     */
    public function membersAction(Request $request, Project $project)
    {
        return array(
            'project' => $project,
        );
    }

    /**
     * Finds and displays a ProjectMember entity.
     *
     * @Route("/member/{id}/show", name="project_member", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function memberAction(ProjectMembership $projectMembership)
    {
        return array(
            'projectMembership' => $projectMembership,
        );
    }

    /**
     * Add member.
     *
     * @Route("/{id}/addmember", name="project_add_member")
     * @Method("GET")
     * @Template("FlowerProjectBundle:ProjectMembership:addmember.html.twig")
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
     * @Template("FlowerProjectBundle:ProjectMembership:addmember.html.twig")
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
     * Displays a form to edit an existing DocPage entity.
     *
     * @Route("/member/{id}/edit", name="project_member_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $projectMembership = $em->getRepository('FlowerModelBundle:Project\ProjectMembership')->find($request->get("id"));
        $editForm = $this->createForm(new ProjectMembershipType(), $projectMembership, array(
            'action' => $this->generateUrl('project_member_update', array('id' => $projectMembership->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($projectMembership->getId(), 'project_member_delete');


        return array(
            'projectMembership' => $projectMembership,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing DocPage entity.
     *
     * @Route("/{id}/update", name="project_member_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerProjectBundle:ProjectMembership:edit.html.twig")
     */
    public function updateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $projectMembership = $em->getRepository('FlowerModelBundle:Project\ProjectMembership')->find($request->get("id"));
        $editForm = $this->createForm(new ProjectMembershipType(), $projectMembership, array(
            'action' => $this->generateUrl('project_member_update', array('id' => $projectMembership->getid())),
            'method' => 'PUT',
        ));

        if ($editForm->handleRequest($request)->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('project_members_full', array('id' => $projectMembership->getProject()->getId())));
        }


        return array(
            'projectMembership' => $projectMembership,
            'form' => $editForm->createView(),
        );
    }
    /**
     * Deletes a ProjectMembership entity.
     *
     * @Route("/projectmembership/{id}/delete", name="project_member_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteProjectMembershipAction(ProjectMembership $membership, Request $request)
    {
        $projectId = $membership->getProject()->getId();
        $form = $this->createDeleteForm($membership->getId(), 'project_member_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($membership);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('project_members_full',array("id" => $projectId)));
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

}
