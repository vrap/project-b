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
     * @Secure(roles="ROLE_ADMIN")
     * @Method("GET")
     * @Route("/archive", name="archive")
     * @Template("ProjectAppBundle:Archive:index.html.twig")
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Secure(roles="ROLE_ADMIN")
     * @Method("DELETE")
     * @Route("/archive/delete", name="archive_delete")
     * @Template("ProjectAppBundle:Archive:delete.html.twig")
     */
    public function deleteAction()
    {
        return array();
    }

    /**
     * @Secure(roles="ROLE_ADMIN")
     * @Method({"GET", "POST"})
     * @Route("/archive/new", name="archive_new")
     * @Template("ProjectAppBundle:Archive:new.html.twig")
     */
    public function newAction()
    {
        $archive = new Archive();
        $form = $this->createForm(new ArchiveType, $archive);
        $request = $this->get('request');
        
        
        if($request->isMethod('POST')) {
            
            $em = $this->getDoctrine()->getManager();
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->persist($archive);
                $em->flush();

                return $this->redirect($this->generateUrl('archive_new'));
            }
        }

    	return $this->render('ProjectAppBundle:Archive:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

}
