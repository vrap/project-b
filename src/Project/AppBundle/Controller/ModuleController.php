<?php

namespace Project\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Project\AppBundle\Entity\Module;
use Project\AppBundle\Form\ModuleType;

class ModuleController extends Controller
{
    /**
     * Display all modules for Manager
     */
    public function indexAction()
    {
        $em                  = $this->getDoctrine()->getManager();
        $repositoryManager   = $em->getRepository('ProjectAppBundle:Manager');
        $repositoryModule    = $em->getRepository('ProjectAppBundle:Module');
        $repositoryFormation = $em->getRepository('ProjectAppBundle:Formation');
        $user                = $this->getUser();
        $modulesList         = $repositoryModule->findBy(array(
            'formation' => $user->getId(),
        ));
        $numberModules       = count($modulesList); 

        return $this->render('ProjectAppBundle:Module:index.html.twig', array(
            'modulesList'   => $modulesList,
            'numberModules' => $numberModules,
        ));
    }

    /**
     * Module creation for Module
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