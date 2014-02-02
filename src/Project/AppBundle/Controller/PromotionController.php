<?php

namespace Project\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Project\AppBundle\Entity\Promotion;
use Project\AppBundle\Form\PromotionType;

class PromotionController extends Controller
{
    /**
     * Display all Promotions of a Formation for Manager
     */
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
     * Promotion creation
     */
    public function createAction()
    {
    	$em                  = $this->getDoctrine()->getManager();
        $repositoryPromotion = $em->getRepository('ProjectAppBundle:Promotion');
    	$promotion           = new Promotion();
    	$formPromotion       = $this->createForm(new PromotionType, $promotion);
    	$request             = $this->get('request');
  
  		$formPromotion->handleRequest($request);
 
        if ($formPromotion->isValid()) {
			$em->persist($promotion);
            $em->flush();
            
            return $this->redirect($this->generateUrl('project_app_user_index'));
        } 

    	return $this->render('ProjectAppBundle:Promotion:create.html.twig', array(
            'formPromotion' => $formPromotion->createView(),
        ));
    }
}