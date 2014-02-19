<?php

namespace Project\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Project\AppBundle\Entity\Feedback;
use Project\AppBundle\Form\FeedbackType;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Feedback controller.
 *
 * @Route("/feedback")
 */
class FeedbackController extends Controller
{

    /**
     * Lists all Feedback entities.
     * @Secure(roles="ROLE_MANAGER, ROLE_STUDENT")
     * @Route("/", name="feedback")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        // Récupération de l'utilisateur connecté
        $user = $this->getUser();
        $userRoles = $user->getRoles();

        // Préparation de la base
        $em = $this->getDoctrine()->getManager();
        
        // Si l'utilisateur est MANAGER
        if(in_array('ROLE_MANAGER', $userRoles)){

            // Récupérer tous les feedBacks
            $entities = $em->getRepository('ProjectAppBundle:Feedback')->findAll();

            // Lui envoyer la vue pour le MANAGER
            return $this->render('ProjectAppBundle:Feedback:index.html.twig', array(
                'entities' => $entities,
            ));

        }

        // Sinon
        // Récupérer les feedbacks de l'utilisateur connecté
        $student = $em->getRepository('ProjectAppBundle:Student')->findOneByUser($this->getUser());
        $entities = $em->getRepository('ProjectAppBundle:Feedback')->findByStudent($student, array('created' => 'DESC'));

        // Lui envoyer la vue pour les STUDENTS
        return $this->render('ProjectAppBundle:Feedback:index.html.twig', array(
            'entities' => $entities,
        ));

    }

    /**
     * Creates a new Feedback entity.
     * @Secure(roles="ROLE_STUDENT")
     * @Route("/", name="feedback_create")
     * @Method("POST")
     * @Template("ProjectAppBundle:Feedback:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Feedback();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $student = $em->getRepository('ProjectAppBundle:Student')->findOneByUser($this->getUser());

            $entity->setStudent($student);
            $entity->setCreated(new \dateTime());

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('feedback'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Feedback entity.
    *
    * @param Feedback $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Feedback $entity)
    {
        $form = $this->createForm(new FeedbackType(), $entity, array(
            'action' => $this->generateUrl('feedback_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Ajouter', 'attr' => array('class' => 'btn btn-second')));

        return $form;
    }

    /**
     * Displays a form to create a new Feedback entity.
     *
     * @Route("/new", name="feedback_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Feedback();
        $form   = $this->createCreateForm($entity);
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Feedback entity.
     *
     * @Route("/{id}", name="feedback_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProjectAppBundle:Feedback')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Feedback entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Feedback entity.
     *
     * @Route("/{id}/edit", name="feedback_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProjectAppBundle:Feedback')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Feedback entity.');
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
    * Creates a form to edit a Feedback entity.
    *
    * @param Feedback $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Feedback $entity)
    {
        $form = $this->createForm(new FeedbackType(), $entity, array(
            'action' => $this->generateUrl('feedback_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Feedback entity.
     *
     * @Route("/{id}", name="feedback_update")
     * @Method("PUT")
     * @Template("ProjectAppBundle:Feedback:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProjectAppBundle:Feedback')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Feedback entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('feedback_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Feedback entity.
     *
     * @Route("/{id}", name="feedback_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ProjectAppBundle:Feedback')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Feedback entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('feedback'));
    }

    /**
     * Creates a form to delete a Feedback entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('feedback_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
