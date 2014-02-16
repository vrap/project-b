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
     * Display all modules for Manager
     *
     * @Route("/", name="module")
     * @Method("GET")
     * @Template("ProjectAppBundle:Module:index.html.twig")
     */
    public function indexAction()
    {
        $em                  = $this->getDoctrine()->getManager();
        $repositoryManager   = $em->getRepository('ProjectAppBundle:Manager');
        $repositoryModule    = $em->getRepository('ProjectAppBundle:Module');
        $repositoryFormation = $em->getRepository('ProjectAppBundle:Formation');
        $user                = $this->getUser();
        $modulesList         = $repositoryModule->findBy(array(
            'promotion' => $user->getId(),
        ));
        $numberModules       = count($modulesList); 
        
        return $this->render('ProjectAppBundle:Module:index.html.twig', array(
            'modulesList'   => $modulesList,
            'numberModules' => $numberModules,
        ));
    }

     /**
     * Creates a new module entity.
     *
     * @Secure(roles="ROLE_MANAGER")
     * @Route("/", name="module_create")
     * @Method("POST")
     * @Template("ProjectAppBundle:module:new.html.twig")
     */
    public function createAction()
    {
    	$em               = $this->getDoctrine()->getManager();
        $repositoryModule = $em->getRepository('ProjectAppBundle:Module');
    	$module           = new Module();
    	$formModule       = $this->createForm(new ModuleType, $module);
    	$request          = $this->get('request');
  
  		$formModule->handleRequest($request);
 
        if ($formModule->isValid()) {
			$em->persist($module);
            $em->flush();
            
            return $this->redirect($this->generateUrl('project_app_user_index'));
        } 

    	return $this->render('ProjectAppBundle:Module:create.html.twig', array(
            'formModule' => $formModule->createView(),
        ));
    }
}