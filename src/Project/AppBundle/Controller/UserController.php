<?php

namespace Project\AppBundle\Controller;

use Project\AppBundle\Entity\Formation;
use Project\AppBundle\Entity\Promotion;
use Project\AppBundle\Entity\Speaker;
use Project\AppBundle\Form\ManagerType;
use Project\AppBundle\Form\SpeakerType;
use Project\AppBundle\Form\StudentType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Project\AppBundle\Entity\User;
use Project\AppBundle\Form\UserType;

/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends Controller
{

    /**
     * Lists all User entities.
     *
     * @Secure(roles={"ROLE_MANAGER"})
     * @Route("/", name="user")
     * @Method("GET")
     * @Template("ProjectAppBundle:User:index.html.twig"))
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('ProjectAppBundle:User')->findAll();

        return array(
            'users' => $users,
        );
    }

    /**
     * Lists all User entities in function of his role.
     *
     * @Secure(roles={"ROLE_MANAGER"})
     * @Route("/list/{type}", requirements={"type" = "student|speaker|manager"}, name="user_list")
     * @Method("GET")
     * @Template("ProjectAppBundle:User:index.html.twig"))
     */
    public function listAction($type)
    {
        $em = $this->getDoctrine()->getManager();

        if('student' == $type) {
            $students = $em->getRepository('ProjectAppBundle:Student')->findAll();

            foreach($students as $student) {
                $users[] = $student->getUser();
            }

            return array(
                    'users' => $users,
            );
        }

        if ('speaker' == $type) {
            $speakers = $em->getRepository('ProjectAppBundle:Speaker')->findAll();

            foreach($speakers as $speaker) {
                $users[] = $speaker->getUser();
            }

            return array(
                    'users' => $users,
            );
        }

        if ('manager' == $type) {
            $managers = $em->getRepository('ProjectAppBundle:Manager')->findAll();

            foreach($managers as $manager) {
                $users[] = $manager->getUser();
            }

            return array(
                    'users' => $users,
            );
        }

        return $this->redirect($this->generateUrl('user'));
    }
    
    /**
     * Creates a new User entity.

     * @Secure(roles={"ROLE_MANAGER"})
     * @Route("/", name="user_create")
     * @Method("POST")
     * @Template("ProjectAppBundle:User:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $user = new User();
        $form = $this->createCreateForm($user);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('user_show', array('id' => $user->getId())));
        }

        return array(
            'user' => $user,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a User entity.
    *
    * @param User $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('user_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Secure(roles={"ROLE_MANAGER"})
     * @Route("/new", name="user_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new User();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
        
    }

    /**
     * Finds and displays a User entity.
     *
     * @Secure(roles={"ROLE_MANAGER"})
     * @Route("/{id}", requirements={"id" = "\d+"}, name="user_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProjectAppBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $promotion = null;
        $formation = null;

        $userRoles = $entity->getRoles();

        if (in_array('ROLE_STUDENT', $userRoles)) {
            $promotion = $em->getRepository('ProjectAppBundle:Student')
                            ->findOneByUser($entity)
                            ->getPromotion();
        }

        if (in_array('ROLE_MANAGER', $userRoles)) {
            $formation = $em->getRepository('ProjectAppBundle:Manager')
                            ->findOneByUser($entity)
                            ->getFormation();
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'promotion'   => $promotion,
            'formation'   => $formation,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Secure(roles={"ROLE_MANAGER"})
     * @Route("/{id}/edit", requirements={"id" = "\d+"}, name="user_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProjectAppBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
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
    * Creates a form to edit a User entity.
    *
    * @param User $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(User $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $userRoles = $entity->getRoles();

        if (in_array('ROLE_STUDENT', $userRoles)) {

            $type = new StudentType();
            $class = $em->getRepository('ProjectAppBundle:Student')
                    ->findOneByUser($entity);

        } elseif (in_array('ROLE_MANAGER', $userRoles)) {

            $type = new ManagerType();
            $class = $em->getRepository('ProjectAppBundle:Manager')
                    ->findOneByUser($entity);

        } elseif (in_array('ROLE_SPEAKER', $userRoles)) {

            $type = new SpeakerType();
            $class = $em->getRepository('ProjectAppBundle:Speaker')
                    ->findOneByUser($entity);

        } else {

            $type = new UserType();
            $class = $entity;

        }

        $form = $this->createForm($type, $class, array(
            'action' => $this->generateUrl('user_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array(
                'label' => 'Modifier',
                'attr' => array(
                    'class' => 'btn btn-second'
                )
             ));

        return $form;
    }
    /**
     * Edits an existing User entity.
     *
     * @Secure(roles={"ROLE_MANAGER"})
     * @Route("/{id}", requirements={"id" = "\d+"}, name="user_update")
     * @Method("PUT")
     * @Template("ProjectAppBundle:User:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProjectAppBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('user_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a User entity.
     *
     * @Secure(roles={"ROLE_MANAGER"})
     * @Route("/{id}", requirements={"id" = "\d+"}, name="user_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ProjectAppBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('user'));
    }

    /**
     * Creates a form to delete a User entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array(
                        'label' => 'Suprimer',
                        'attr' => array(
                            'class' => 'btn btn-primary'
                        )
                ))
            ->getForm()
        ;
    }

    /**
     * User profile
     *
     * @Secure(roles={"ROLE_MANAGER", "ROLE_SPEAKER", "ROLE_STUDENT"})
     * @Route("/profile", name="user_profile")
     * @Method("GET")
     * @Template("ProjectAppBundle:User:profile.html.twig")
     */
    public function profileAction() {
        $user = $this->getUser();

        return array(
            'user' => $user
        );
    }

    /**
     * Update user profile
     *
     * @Secure(roles={"ROLE_MANAGER", "ROLE_SPEAKER", "ROLE_STUDENT"})
     * @Route("/profile", name="user_profile_update")
     * @Method("POST")
     * @Template()
     */
    public function updateProfileAction(Request $request) {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $new_pw = $request->request->get('new_pw');
        $confirm_new_pw = $request->request->get('confirm_new_pw');

        if('' == $confirm_new_pw && '' != $new_pw) {
            $this->get('session')->getFlashBag()->add('error', 'Veuillez confirmer votre mot de passe.');

            return $this->redirect($this->generateUrl('user_profile'));
        }

        if('' != $confirm_new_pw && '' == $new_pw) {
            $this->get('session')->getFlashBag()->add('error', 'Veuillez renseigner votre mot de passe.');

            return $this->redirect($this->generateUrl('user_profile'));
        }

        if($confirm_new_pw != $new_pw) {
            $this->get('session')->getFlashBag()->add('error', 'Le mot de passe et sa confirmation sont différents.');

            return $this->redirect($this->generateUrl('user_profile'));
        }

        if(strlen($confirm_new_pw) < 8 || strlen($new_pw) < 8) {
            $this->get('session')->getFlashBag()->add('error', 'Votre mot de passe doit contenir 8 caractères au minimum.');

            return $this->redirect($this->generateUrl('user_profile'));
        }

        if('' != $confirm_new_pw && '' != $new_pw) {
            // FOS User manipulator
            $manipulator = $this->get('fos_user.util.user_manipulator');
            // Change password and crypt it
            $manipulator->changePassword($user->getUsername(), $new_pw);
            // Saving in database
            $em->persist($user);
            $em->flush();

            $this->get('session')->getFlashBag()->add('info', 'Modifications enregistrées.');

            return $this->redirect($this->generateUrl('user_profile'));
        }

        return $this->redirect($this->generateUrl('user_profile'));
    }
}
