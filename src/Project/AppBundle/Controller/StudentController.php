<?php

namespace Project\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Project\AppBundle\Entity\Student;
use Project\AppBundle\Form\StudentType;

/**
 * Student controller.
 *
 * @Route("/student")
 */
class StudentController extends Controller
{

    /**
     * Creates a new Student entity.
     *
     * @Secure(roles={"ROLE_MANAGER"})
     * @Route("/", name="student_create")
     * @Method("POST")
     * @Template("ProjectAppBundle:User:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Student();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $entity->getUser()->setEnabled(1);
            $entity->getUser()->setRoles(array('ROLE_STUDENT'));
            $this->get('session')->getFlashBag()->add('info', 'L\'utilisateur à bien été ajouté.');
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user'));
        }

        return array(
            'entity' => $entity,
            'title'  => 'Créer un étudiant',
            'form'   => $form->createView(),
        );

    }

    /**
    * Creates a form to create a Student entity.
    *
    * @param Student $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Student $entity)
    {
        $form = $this->createForm(new StudentType(), $entity, array(
            'action' => $this->generateUrl('student_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Ajouter', 'attr' => array('class' => 'btn btn-second')));

        return $form;
    }

    /**
     * Displays a form to create a new Student entity.
     *
     * @Secure(roles={"ROLE_MANAGER"})
     * @Route("/new", name="user_student_new")
     * @Method("GET")
     * @Template("ProjectAppBundle:Student:new.html.twig")
     */
    public function newAction()
    {
        $entity = new Student();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'title'  => 'Créer un étudiant',
            'form'   => $form->createView(),
        );
    }
}
