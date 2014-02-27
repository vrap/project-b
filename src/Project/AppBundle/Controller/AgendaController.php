<?php

namespace Project\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Project\AppBundle\Entity\Lesson;
use Symfony\Component\HttpFoundation\Response;
use Project\AppBundle\Form\SpeakerType;
use Project\AppBundle\Form\ModuleType;
use Project\AppBundle\Entity\Speaker;
use Project\AppBundle\Entity\Module;

/**
 * Archive controller.
 *
 * @Route("/agenda")
 */
class AgendaController extends Controller
{
    /**
     * @Secure({"ROLE_STUDENT", "ROLE_SPEAKER", "ROLE_MANAGER"})
     * @Route("/", name="project_app_agenda_index")
     * @Method("GET")
     * @Template("ProjectAppBundle:Agenda:index.html.twig")
     */
    public function indexAction()
    {
        $speakerEntity = new Speaker();
        
        $formSpeaker = $this->createForm(new SpeakerType(), $speakerEntity, array());
        $formSpeaker->add('user', 'entity', array(
            'query_builder' => function($entity) { return $entity->createQueryBuilder('p')->orderBy('p.id', 'ASC'); },
            'property' => 'user',
            'class' => 'ProjectAppBundle:Speaker',
        ));
            
        $moduleEntity = new Module();
        $moduleForm = $this->createForm(new ModuleType(), $moduleEntity, array());
        $moduleForm->add('name', 'entity', array(
            'query_builder' => function($entity) { return $entity->createQueryBuilder('p')->orderBy('p.id', 'ASC'); },
            'property' => 'name',
            'class' => 'ProjectAppBundle:Module',
        ));

        return array('formSpeaker' => $formSpeaker->createView(), 'formModule' => $moduleForm->createView());
    }
    
    /**
     * @Secure("ROLE_MANAGER")
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

        $entity = new Lesson();
        
        $em = $this->getDoctrine()->getManager();
        // Check if Speaker with given id exist
        $speakerEntity = $em->getRepository('ProjectAppBundle:Speaker')->find($data->speakerId);
        if(!$speakerEntity) {
            return new Response(json_encode(false));
        }
        
        // Check if Module with given id exist
        $moduleEntity = $em->getRepository('ProjectAppBundle:Module')->find($data->moduleId);
        if(!$moduleEntity) {
            return new Response(json_encode(false));
        }
        
        if($data->name == '') {
            return new Response(json_encode(false));
        }
        
        $entity->setName($data->name);
        $entity->setStartDate($startDate);
        $entity->setEndDate($endDate);
        $entity->setTimecard(1);
        $entity->setSpeaker($speakerEntity);
        $entity->setModule($moduleEntity);
        
        $em->persist($entity);
        $em->flush();
        
        // Save in log file
        $log = $this->get('log');
        $log->createLog( array( 'fields' => array('Name' => $data->name),
                                'module' => 'lesson',
                                'username' => $this->getUser()->getUsername()),
                                'add'
                       );

        return $response;
    }


    /**
     * @Secure("ROLE_MANAGER")
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

        // Check if lesson id has been found
        if (!$entity) {
            $response = new Response(json_encode(false));
        } else {
            // Save in log file
            $log = $this->get('log');
            $log->createLog( array( 'fields' => array('Name' => $entity->getName(), 'id' => $entity->getId()),
                                    'module' => 'lesson',
                                    'username' => $this->getUser()->getUsername()),
                                    'delete'
                       );
            // Remove from database
            $em->remove($entity);
            $em->flush();
        }

        return $response;
    }
}
