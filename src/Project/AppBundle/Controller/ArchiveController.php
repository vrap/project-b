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
     * @Secure(roles={"ROLE_ADMIN", "ROLE_MANAGER"})
     * @Method("GET")
     * @Route("/archive", name="archive")
     * @Template("ProjectAppBundle:Archive:index.html.twig")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = array();
        $archives = $em->getRepository('ProjectAppBundle:Archive')->findBy(array(), array('year' => 'DESC'));

        foreach ($archives as $archive) {
            $entities[] = array(
                'archive' => $archive,
                'promotions' => $em->getRepository('ProjectAppBundle:Promotion')->findByArchive($archive)
            );
        }

        return array(
            'entities' => $entities,
        );
    }

    /**
     * @Secure(roles="ROLE_ADMIN")
     * @Method("DELETE")
     * @Route("/archive/delete", name="archive_delete")
     * @Template()
     */
    public function deleteAction()
    {

    }

    /**
     * @Secure(roles={"ROLE_ADMIN", "ROLE_MANAGER"})
     * @Method({"GET", "POST"})
     * @Route("/archive/new", name="archive_new")
     * @Template("ProjectAppBundle:Archive:new.html.twig")
     */
    public function newAction()
    {
        $archive = new Archive();
        $form = $this->createForm(new ArchiveType, $archive);
        $form->add('submit', 'submit', array(
                'label' => 'Enregistrer',
                'attr'  => array(
                        'class' => 'btn btn-second'
                )
        ));

        $request = $this->get('request');

        if($request->isMethod('POST')) {
            
            $em = $this->getDoctrine()->getManager();
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->persist($archive);
                $em->flush();

                $this->get('session')->getFlashBag()->add('info', 'Archive créée.');

                return $this->redirect($this->generateUrl('archive_new'));
            }

            $this->get('session')->getFlashBag()->add('error', 'Une erreur est survenue. Merci de vérifier vos informations');
        }

    	return $this->render('ProjectAppBundle:Archive:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

}
