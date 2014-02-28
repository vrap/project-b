<?php

namespace Project\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Project\AppBundle\Entity\Promotion;
use Project\AppBundle\Form\PromotionType;
use ZipArchive;

/**
 * Promotion controller.
 *
 * @Route("/promotion")
 */
class PromotionController extends Controller
{
    /**
     * Lists all Promotion entities.
     *
     * @Secure(roles="ROLE_MANAGER")
     * @Route("/", name="promotion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em       = $this->getDoctrine()->getManager();
        $user     = $this->getUser();
        $manager  = $em->getRepository('ProjectAppBundle:Manager')->findOneBy(array(
            'user' => $user->getId()
        ));

        $promotions = $em->getRepository('ProjectAppBundle:Promotion')->findBy(array(
            'formation' => $manager->getFormation()->getId()
        ));

        $this->get('session')->remove('promotion');

        return array(
            'promotions' => $promotions,
        );
    }

    /**
     * Manage a promotion.
     *
     * @Secure(roles="ROLE_MANAGER")
     * @Route("/{id}/manage", name="promotion_manage")
     * @Method("GET")
     *
     * @param Int $id Id of promotion to manage.
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function manageAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $promotion = $em->getRepository('ProjectAppBundle:Promotion')->findOneBy(array('id' => $id));

        $this->get('session')->set('promotion', $id);

        return new RedirectResponse($this->generateUrl('module'));
    }

    /**
     * Creates a new Promotion entity.
     *
     * @Secure(roles="ROLE_MANAGER")
     * @Route("/", name="promotion_create")
     * @Method("POST")
     * @Template("ProjectAppBundle:Promotion:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Promotion();
        $form   = $this->createCreateForm($entity);
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            $user     = $this->getUser();
            $manager  = $em->getRepository('ProjectAppBundle:Manager')->findOneBy(array(
                'user' => $user->getId()
            ));
            $formation = $em->getRepository('ProjectAppBundle:Formation')->find($manager->getFormation()->getId());

            $entity->setFormation($formation);
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('info', 'Promotion enregistrée.');

            return $this->redirect($this->generateUrl('promotion', array(
                'id' => $entity->getId())
            ));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Promotion entity.
     *
     * @Secure(roles="ROLE_MANAGER")
     *
     * @param Promotion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Promotion $entity)
    {
        $form = $this->createForm(new PromotionType(), $entity, array(
            'action' => $this->generateUrl('promotion_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Enregistrer',
            'attr'  => array(
                'class' => 'btn btn-second'
            )
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Promotion entity.
     *
     * @Secure(roles="ROLE_MANAGER")
     * @Route("/new", name="promotion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Promotion();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Promotion entity.
     *
     * @Secure(roles="ROLE_MANAGER")
     * @Route("/{id}", name="promotion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
    }

    /**
     * Displays a form to edit an existing Promotion entity.
     *
     * @Secure(roles="ROLE_MANAGER")
     * @Route("/{id}/edit", name="promotion_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em     = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ProjectAppBundle:Promotion')->find($id);

        if (! $entity) {
            throw $this->createNotFoundException('Unable to find Promotion entity.');
        }

        $editForm   = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Promotion entity.
     *
     * @Secure(roles="ROLE_MANAGER")
     *
     * @param Promotion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Promotion $entity)
    {
        $form = $this->createForm(new PromotionType(), $entity, array(
            'action' => $this->generateUrl('promotion_update', array(
                'id' => $entity->getId()
            )),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Modifier',
            'attr'  => array(
                'class' => 'btn btn-second'
            )
        ));

        return $form;
    }

    /**
     * Edits an existing Promotion entity.
     *
     * @Secure(roles="ROLE_MANAGER")
     * @Route("/{id}", name="promotion_update")
     * @Method("PUT")
     * @Template("ProjectAppBundle:Promotion:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em     = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ProjectAppBundle:Promotion')->find($id);

        if (! $entity) {
            throw $this->createNotFoundException('Unable to find Promotion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm   = $this->createEditForm($entity);

        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add('info', 'Modifications enregistrées.');

            return $this->redirect($this->generateUrl('promotion_edit', array(
                'id' => $id
            )));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Promotion entity.
     *
     * @Secure(roles="ROLE_MANAGER")
     * @Route("/{id}", name="promotion_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em     = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ProjectAppBundle:Promotion')->find($id);

            if (! $entity) {
                throw $this->createNotFoundException('Unable to find Promotion entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('info', 'Promotion supprimée.');
        }

        return $this->redirect($this->generateUrl('promotion'));
    }

    /**
     * Creates a form to delete a Promotion entity by id.
     *
     * @Secure(roles="ROLE_MANAGER")
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('promotion_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array(
                    'label' => 'Supprimer',
                    'attr'  => array(
                        'class' => 'btn btn-primary btn-small'
                    )
                ))
            ->getForm()
        ;
    }

    /**
     * Create an archine of a promotion.
     *
     * @Secure(roles="ROLE_MANAGER")
     * @Route("/{id}/archive", name="promotion_archive")
     * @Method("GET")
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function archiveAction($id)
    {
        // Retrieve the promotion to json format and convert it to an array
        $em            = $this->getDoctrine()->getManager();
        $jsonPromotion = $em->getRepository('ProjectAppBundle:Promotion')->toJson($id);
        $promotion     = json_decode($jsonPromotion);

        // Define the temporary path and the archive name
        $zipName     = 'archive-'.$promotion->promotion.'.zip';
        $tempPath    = $this->get('kernel')->getCacheDir().'/'.$zipName;

        // Create the archive
        $zip = new \ZipArchive();
        $zip->open($tempPath, ZIPARCHIVE::CREATE);

        // Add promotion csv to the archive
        $zip->addFromString('promotion.csv', $jsonPromotion);

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
}
