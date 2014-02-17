<?php

namespace Project\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Project\AppBundle\Entity\Lesson;
use Symfony\Component\HttpFoundation\Response;

/**
 * Archive controller.
 *
 * @Route("/agenda")
 */
class AgendaController extends Controller
{
    /**
     * @Secure("ROLE_USER")
     * @Route("/")
     * @Method("GET")
     * @Template("ProjectAppBundle:Agenda:index.html.twig")
     */
    public function indexAction()
    {
        return array();
    }
    
    /**
     * @Route("/add", name="agenda_add_lesson")
     * @Template()
     * @Method("POST")
     * 
     * @return \Symfony\Component\HttpFoundation\Response (bool)
     */
    public function addAction($data)
    {
        // Change return header as json
        $response = new Response(json_encode(true));
        $response->headers->set('Content-Type', 'application/json');

        // Decode received data
        $data = json_decode($data);

        // Convert retrieve data to DateTime to avoid error
        $startDate = new \DateTime($data->startDate);
        $endDate = new \DateTime($data->endDate);

        // Fill the entity and save it
        $entity = new Lesson();
        $em = $this->getDoctrine()->getManager();

        $entity->setName($data->name);
        $entity->setStartDate($startDate);
        $entity->setEndDate($endDate);
        $entity->setTimecard(1);

        $em->persist($entity);
        $em->flush();

        return $response;
    }


    /**
     * @Route("/delete", name="agenda_delete_lesson")
     * @Template()
     * @Method("POST")
     * 
     * @return \Symfony\Component\HttpFoundation\Response (bool)
     */
    public function deleteAction($id)
    {
        // Change return header as json
        $response = new Response(json_encode(true));
        $response->headers->set('Content-Type', 'application/json');

        // Execute select to find the Lesson
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ProjectAppBundle:Lesson')->find($id);

        // Return false if the Lesson doesn't exists else remove it
        if (!$entity) {
            $response = new Response(json_encode(false));
        } else {
            $em->remove($entity);
            $em->flush();
        }

        return $response;
    }
}
