<?php

namespace Project\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Project\AppBundle\Entity\Manager;
use Project\AppBundle\Form\ManagerType;

class ManagerController extends Controller
{
    public function indexAction()
    {
        $em                  = $this->getDoctrine()->getManager();
        $repositoryPromotion = $em->getRepository('ProjectAppBundle:Promotion');
        $user                = $this->getUser();
        $promotionsList      = $repositoryPromotion->findBy(array(
            'formation' => 1,
        ));
        
        return $this->render('ProjectAppBundle:Promotion:index.html.twig', array(
            'promotionsList' => $promotionsList,
        ));
    }

    /**
     * Creates a new Manager entity.
     *
     * @Route("/", name="manager_create")
     * @Method("POST")
     * @Template("ProjectAppBundle:User:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Manager();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $entity->getUser()->setEnabled(1);
            $entity->getUser()->setRoles(array('ROLE_MANAGER'));
            $entity->setIsAdministrator(0);

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user'));
        }

        return array(
            'entity' => $entity,
            'title'  => 'Créer un Responsable',
            'form'   => $form->createView(),
        );

    }

    /**
    * Creates a form to create a Manager entity.
    *
    * @param Student $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Manager $entity)
    {
        $form = $this->createForm(new ManagerType(), $entity, array(
            'action' => $this->generateUrl('speaker_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Ajouter', 'attr' => array('class' => 'btn btn-second')));

        return $form;
    }

    /**
     * Displays a form to create a new Manager entity.
     *
     * @Route("/new", name="user_manager_new")
     * @Method("GET")
     * @Template("ProjectAppBundle:User:new.html.twig")
     */
    public function newAction()
    {
        $entity = new Manager();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'title'  => 'Créer un Responsable',
            'form'   => $form->createView(),
        );
    }


}