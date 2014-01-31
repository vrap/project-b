<?php

namespace Project\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Project\AppBundle\Entity\Module;
use Project\AppBundle\Form\ModuleType;

class ModuleController extends Controller
{
    public function indexAction()
    {

    }

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