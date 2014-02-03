<?php

namespace Project\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
}