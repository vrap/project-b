<?php

namespace Project\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Project\AppBundle\Entity\Formation;
use Project\AppBundle\Form\FormationType;

/**
 * Formation controller.
 *
 * @Route("/formation")
 */
class FormationController extends Controller
{
    /**
     * Lists all Formation entities.
     *
     * @Secure(roles="ROLE_ADMIN")
     * @Route("/", name="formation")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em       = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('ProjectAppBundle:Formation')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Formation entity.
     *
     * @Secure(roles="ROLE_ADMIN")
     * @Route("/", name="formation_create")
     * @Method("POST")
     * @Template("ProjectAppBundle:Formation:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Formation();
        $form   = $this->createCreateForm($entity);
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('formation', array(
                'id' => $entity->getId())
            ));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Formation entity.
    *
    * @Secure(roles="ROLE_ADMIN")
    *
    * @param Formation $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Formation $entity)
    {
        $form = $this->createForm(new FormationType(), $entity, array(
            'action' => $this->generateUrl('formation_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Create'
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Formation entity.
     *
     * @Secure(roles="ROLE_ADMIN")
     * @Route("/new", name="formation_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Formation();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Formation entity.
     *
     * @Secure(roles="ROLE_ADMIN")
     * @Route("/{id}", name="formation_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {   
    }

    /**
     * Displays a form to edit an existing Formation entity.
     *
     * @Secure(roles="ROLE_ADMIN")
     * @Route("/{id}/edit", name="formation_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em     = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ProjectAppBundle:Formation')->find($id);

        if (! $entity) {
            throw $this->createNotFoundException('Unable to find Formation entity.');
        }

        $editForm   = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Formation entity.
    *
    * @Secure(roles="ROLE_ADMIN")
    *
    * @param Formation $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Formation $entity)
    {
        $form = $this->createForm(new FormationType(), $entity, array(
            'action' => $this->generateUrl('formation_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Update'
        ));

        return $form;
    }

    /**
     * Edits an existing Formation entity.
     *
     * @Secure(roles="ROLE_ADMIN")
     * @Route("/{id}", name="formation_update")
     * @Method("PUT")
     * @Template("ProjectAppBundle:Formation:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em     = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ProjectAppBundle:Formation')->find($id);

        if (! $entity) {
            throw $this->createNotFoundException('Unable to find Formation entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm   = $this->createEditForm($entity);
        
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('formation_edit', array(
                'id' => $id
            )));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Formation entity.
     *
     * @Secure(roles="ROLE_ADMIN")
     * @Route("/{id}", name="formation_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em     = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ProjectAppBundle:Formation')->find($id);

            if (! $entity) {
                throw $this->createNotFoundException('Unable to find Formation entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('formation'));
    }

    /**
     * Creates a form to delete a Formation entity by id.
     *
     * @Secure(roles="ROLE_ADMIN")
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('formation_delete', array(
                'id' => $id
            )))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array(
                'label' => 'Delete'
            ))
            ->getForm();
    }
}
