<?php

namespace Project\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Project\AppBundle\Entity\Corporation;
use Project\AppBundle\Form\CorporationType;

/**
 * Corporation controller.
 *
 * @Route("/corporation")
 */
class CorporationController extends Controller
{

    /**
     * Lists all Corporation entities.
     *
     * @Route("/", name="corporation")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ProjectAppBundle:Corporation')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Corporation entity.
     *
     * @Route("/", name="corporation_create")
     * @Method("POST")
     * @Template("ProjectAppBundle:Corporation:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Corporation();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('corporation_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Corporation entity.
    *
    * @param Corporation $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Corporation $entity)
    {
        $form = $this->createForm(new CorporationType(), $entity, array(
            'action' => $this->generateUrl('corporation_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Corporation entity.
     *
     * @Route("/new", name="corporation_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Corporation();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Corporation entity.
     *
     * @Route("/{id}", name="corporation_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProjectAppBundle:Corporation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Corporation entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Corporation entity.
     *
     * @Route("/{id}/edit", name="corporation_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProjectAppBundle:Corporation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Corporation entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Corporation entity.
    *
    * @param Corporation $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Corporation $entity)
    {
        $form = $this->createForm(new CorporationType(), $entity, array(
            'action' => $this->generateUrl('corporation_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Corporation entity.
     *
     * @Route("/{id}", name="corporation_update")
     * @Method("PUT")
     * @Template("ProjectAppBundle:Corporation:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProjectAppBundle:Corporation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Corporation entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('corporation_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Corporation entity.
     *
     * @Route("/{id}", name="corporation_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ProjectAppBundle:Corporation')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Corporation entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('corporation'));
    }

    /**
     * Creates a form to delete a Corporation entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('corporation_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
