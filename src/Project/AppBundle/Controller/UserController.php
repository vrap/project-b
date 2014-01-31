<?php

namespace Project\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Project\AppBundle\Entity\Formation;
use Project\AppBundle\Form\FormationType;

class UserController extends Controller
{
    public function indexAction()
    {
    	$em                  = $this->getDoctrine()->getManager();
        $repositoryFormation = $em->getRepository('ProjectAppBundle:Formation');
    	$formation           = new Formation();
    	$formFormation       = $this->createForm(new FormationType, $formation);
    	$request             = $this->get('request');
  
  		$formFormation->handleRequest($request);
 
        if ($formFormation->isValid()) {
			$em->persist($formation);
            $em->flush();
            
            return $this->redirect($this->generateUrl('project_app_user_index'));
        } 

    	return $this->render('ProjectAppBundle:Formation:create.html.twig', array(
            'formFormation' => $formFormation->createView(),
        ));
    }
}