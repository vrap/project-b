<?php

namespace Project\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Project\AppBundle\Entity\Evaluation;
use Project\AppBundle\Entity\Criterion;
use Project\AppBundle\Form\CriterionType;
use Symfony\Component\Process\Exception\InvalidArgumentException;

/**
 * Criterion controller.
 *
 * @Route()
 */
class CriterionController extends Controller
{

    /**
     * Lists all Criterion entities.
     *
     * @Secure(roles="ROLE_SPEAKER")
     * @Route("/criterion/", name="criterion")
     * @Method("GET")
     * @Template("ProjectAppBundle:Criterion:index.html.twig")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ProjectAppBundle:Criterion')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Criterion entity.
     *
     * @Secure(roles="ROLE_SPEAKER")
     * @Route("/evaluation/{id_eval}/criterion/", name="criterion_create")
     * @Method("POST")
     * @Template("ProjectAppBundle:Criterion:new.html.twig")
     */
    public function createAction(Request $request, $id_eval)
    {
        $entity = new Criterion();
        $form = $this->createCreateForm($entity, $id_eval);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $eval = $em->getRepository('ProjectAppBundle:Evaluation')->find($id_eval);
            if(!$eval) {
                throw new InvalidArgumentException('This evaluation doesn\'t exist');
            }
            $entity->setEvaluation($eval);
            $em->persist($entity);
            $em->flush();

            if('submit' == $form->getClickedButton()->getName()) {

                return $this->redirect($this->generateUrl('speaker_evaluations'));
            } else if('crit_new' == $form->getClickedButton()->getName()) {

                return $this->redirect($this->generateUrl('criterion_new', array(
                        'id_eval' => $id_eval
                )));
            }

            return $this->redirect($this->generateUrl('speaker_evaluations'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Criterion entity.
    *
    * @param Criterion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Criterion $entity, $id_eval)
    {
        $form = $this->createForm(new CriterionType(), $entity, array(
            'action' => $this->generateUrl('criterion_create', array('id_eval' => $id_eval)),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Criterion entity.
     *
     * @Secure(roles="ROLE_SPEAKER")
     * @Route("/evaluation/{id_eval}/criterion/new", name="criterion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($id_eval)
    {
        $entity = new Criterion();
        $form   = $this->createCreateForm($entity, $id_eval);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Criterion entity.
     *
     * @Secure(roles="ROLE_SPEAKER")
     * @Route("/criterion/{id}", name="criterion_show")
     * @Method("GET")
     * @Template("ProjectAppBundle:Criterion:show.html.twig")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProjectAppBundle:Criterion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Criterion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Criterion entity.
     *
     * @Secure(roles="ROLE_SPEAKER")
     * @Route("/criterion/{id}/edit", name="criterion_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProjectAppBundle:Criterion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Criterion entity.');
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
    * Creates a form to edit a Criterion entity.
    *
    * @param Criterion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Criterion $entity)
    {
        $form = $this->createForm(new CriterionType(), $entity, array(
            'action' => $this->generateUrl('criterion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Criterion entity.
     *
     * @Secure(roles="ROLE_SPEAKER")
     * @Route("/criterion/{id}", name="criterion_update")
     * @Method("PUT")
     * @Template("ProjectAppBundle:Criterion:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProjectAppBundle:Criterion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Criterion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('criterion_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Criterion entity.
     *
     * @Secure(roles="ROLE_SPEAKER")
     * @Route("/criterion/{id}", name="criterion_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ProjectAppBundle:Criterion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Criterion entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('criterion'));
    }

    /**
     * Creates a form to delete a Criterion entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('criterion_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
