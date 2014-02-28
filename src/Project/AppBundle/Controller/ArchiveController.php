<?php

namespace Project\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Project\AppBundle\Entity\Archive;
use Project\AppBundle\Entity\ArchiveBilan;
use Project\AppBundle\Form\ArchiveType;
use ZipArchive;

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

    /**
     * Download an archive
     *
     * @Secure(roles="ROLE_MANAGER")
     * @Route("/{id}/archive", name="archive_download")
     * @Method("GET")
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function downloadAction($id)
    {
        $em      = $this->getDoctrine()->getManager();
        $archive = $em->getRepository('ProjectAppBundle:Archive')->findOneBy(array('id' => $id));

        if ($archive) {
            // Define the temporary path and the archive name
            $zipName     = 'archive-'.$archive->getName().'.'.uniqid().'.zip';
            $tempPath    = $this->get('kernel')->getCacheDir().'/'.$zipName;

            // Create the archive
            $zip = new \ZipArchive();
            $zip->open($tempPath, ZIPARCHIVE::CREATE);

            $bilans = $archive->getArchiveBilans();

            foreach ($bilans as $bilan) {
                $content = stream_get_contents($bilan->getContent());

                $zip->addFromString($bilan->getType(), $content);
            }

            $zip->close();

            // Create a response
            $response = new Response();

            // Generate a content disposition according to filename
            $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $zipName);

            // Set headers
            $response->headers->set('Cache-Control', 'private');
            $response->headers->set('Content-type', 'application/octet-stream');
            $response->headers->set('Content-Disposition', $disposition);
            $response->headers->set('Content-length', filesize($tempPath));

            // Send headers before outputting anything
            $response->sendHeaders();

            // Set the zip file as content
            $response->setContent(file_get_contents($tempPath));

            // Remove the temporary zip
            unlink($tempPath);

            // Return the response
            return $response;
        }

        // If error occured, show a message
        $this->get('session')->getFlashBag()->add('error', 'Impossible de télécharger cette archive.');

        return $this->redirect($this->generateUrl('archive'));
    }
}
