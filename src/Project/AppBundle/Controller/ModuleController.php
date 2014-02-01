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
        $repositoryModule    = $em->getRepository('ProjectAppBundle:Module');
        $repositoryFormation = $em->getRepository('ProjectAppBundle:Formation');
        $modulesList         = $repositoryModule->findAll();
        
        return $this->render('ProjectAppBundle:Module:index.html.twig', array(
            'modulesList' => $modulesList,
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