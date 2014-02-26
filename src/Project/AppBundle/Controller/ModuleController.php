<?php

namespace Project\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Project\AppBundle\Entity\Module;
use Project\AppBundle\Form\ModuleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Process\Exception\InvalidArgumentException;

/**
 * Module controller.
 *
 * @Route("/module")
 */
class ModuleController extends Controller
{
    /**
     * Display all modules.
     *
     * @Route("/", name="module")
     * @Method("GET")
     * @Template("ProjectAppBundle:Module:index.html.twig")
     */
    public function indexAction()
    {
        $em               = $this->getDoctrine()->getManager();
        $repositoryModule = $em->getRepository('ProjectAppBundle:Module');
        $modulesList      = $repositoryModule->findBy(array(
            'promotion' => $this->get('session')->get('promotion')
        ));
        $deleteForms = array();

        foreach ($modulesList as $module) {
            $deleteForms[$module->getId()] = $this->createDeleteForm($module->getId())->createView();
        }
        
        return $this->render('ProjectAppBundle:Module:index.html.twig', array(
            'modulesList' => $modulesList,
            'deleteForms' => $deleteForms
        ));
    }

    /**
     * Displays a form to create a new Module entity.
     *
     * @Secure(roles="ROLE_MANAGER")
     * @Route("/new", name="module_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Module();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Module entity.
     *
     * @param Module $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Module $entity)
    {
        $form = $this->createForm(new ModuleType(), $entity, array(
            'action' => $this->generateUrl('module_create'),
            'method' => 'POST'
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Enregistrer',
            'attr' => array('class' => 'btn btn-second')
        ));

        return $form;
    }

     /**
     * Creates a new module entity.
     *
     * @Secure(roles="ROLE_MANAGER")
     * @Route("/", name="module_create")
     * @Method("POST")
     * @Template("ProjectAppBundle:Module:new.html.twig")
     */
    public function createAction(Request $request)
    {
    	$module           = new Module();
    	$form             = $this->createCreateForm($module);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $promotion = $em->getRepository('ProjectAppBundle:Promotion')
                ->findOneBy(array(
                                  'id' => $this->get('session')->get('promotion')
                                  ));
 
            $module->setPromotion($promotion);

			$em->persist($module);
            $em->flush();

            return $this->redirect($this->generateUrl('module'));
        }

        return array(
            'entity' => $module,
            'form'   => $form->createView()
        );
    }

    /**
     * Deletes a Module entity.
     *
     * @Secure(roles="ROLE_MANAGER")
     * @Route("/{id}", name="module_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em     = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ProjectAppBundle:Module')->findOneBy(
                          array(
                              'id' => $id,
                              'promotion' => $this->get('session')->get('promotion')
                          )
                      );

            if (! $entity) {
                throw $this->createNotFoundException('Unable to find Module entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('module'));
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
            ->setAction($this->generateUrl('module_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
            ;
    }
}