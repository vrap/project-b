<?php

namespace Project\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Project\AppBundle\Entity\Archive;
use Project\AppBundle\Form\ArchiveType;

/**
 * Archive controller.
 *
 * @Route()
 */
class ArchiveController extends Controller
{
    /**
     * @Secure(roles="ROLE_MANAGER")
     * @Method("GET")
     * @Route("/archive/index", name="archive")
     * @Template("ProjectAppBundle:Archive:index.html.twig")
     */
    public function indexAction()
    {
        $module = new Archive();
        $form = $this->createForm(new ArchiveType, $module);
        $em = $this->getDoctrine()->getManager();
    	$request = $this->get('request');
        $form->handleRequest($request);
 
        if ($form->isValid()) {
            $em->persist($module);
            $em->flush();
            
            return $this->redirect($this->generateUrl('project_app_user_index'));
        }

    	return $this->render('ProjectAppBundle:Archive:index.html.twig', array(
            'formModule' => $form->createView(),
        ));
    }

    /**
     * @Secure(roles="ROLE_MANAGER")
     * @Method("GET")
     * @Route("/archive/delete", name="archive_delete")
     * @Template("ProjectAppBundle:Archive:delete.html.twig")
     */
    public function deleteAction()
    {
    }

    /**
     * @Secure(roles="ROLE_MANAGER")
     * @Method("GET")
     * @Route("/archive/new", name="archive_new")
     * @Template("ProjectAppBundle:Archive:new.html.twig")
     */
    public function newAction()
    {
    }

}
