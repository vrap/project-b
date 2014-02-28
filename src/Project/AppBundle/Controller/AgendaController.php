<?php

namespace Project\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    JMS\SecurityExtraBundle\Annotation\Secure,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Project\AppBundle\Entity\Lesson,
    Symfony\Component\HttpFoundation\Response,
    Project\AppBundle\Form\SpeakerType,
    Project\AppBundle\Form\ModuleType,
    Project\AppBundle\Entity\Speaker,
    Project\AppBundle\Entity\Module,
    Project\AppBundle\Entity\LessonStudent,
    Project\AppBundle\Entity\SpeakerLesson;

/**
 * Archive controller.
 *
 * @Route("/agenda")
 */
class AgendaController extends Controller
{
    /**
     * @Secure({"ROLE_STUDENT", "ROLE_SPEAKER", "ROLE_MANAGER"})
     * @Route("/", name="agenda_index")
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

        $entityLesson = new Lesson();
        
        $em = $this->getDoctrine()->getManager();
        // Check if Speaker with given id exist
        $speakerEntity = $em->getRepository('ProjectAppBundle:Speaker')->find($data->speakerId);
        if(!$speakerEntity) {
            $this->get('session')->getFlashbag()->add('error', "Le nom de l'intervenant n'a pas été trouvé.");
            return new Response(json_encode(false));
        }
        
        // Check if Module with given id exist
        $moduleEntity = $em->getRepository('ProjectAppBundle:Module')->find($data->moduleId);
        if(!$moduleEntity) {
            $this->get('session')->getFlashbag()->add('error', "Le module n'a pas été trouvé.");
            return new Response(json_encode(false));
        }
        
        if($data->name == '') {
            $this->get('session')->getFlashbag()->add('error', 'Le nom de la leçon ne peut-être vide.');
            return new Response(json_encode(false));
        }
        
        // Set variables for lesson
        $entityLesson->setName($data->name);
        $entityLesson->setStartDate($startDate);
        $entityLesson->setEndDate($endDate);
        $entityLesson->setTimecard(1);
        $entityLesson->setSpeaker($speakerEntity);
        $entityLesson->setModule($moduleEntity);
        
        // Insert into Lesson table
        $em->persist($entityLesson);
        $em->flush();
        
        // Retrieve module informations which help to retrieve promotion
        $module = $em->getRepository('ProjectAppBundle:Module')->find($data->moduleId);
        $promotion = $module->getPromotion();
        
        // Retrieve all student for current promotion
        $students = $em->getRepository('ProjectAppBundle:Student')
                       ->findByPromotion($promotion);
        
        /*$speakerLesson = new SpeakerLesson();
        $speakerLesson->set$entityLesson->getId();
        $speakerEntity->getId();*/

        // Insert in table LessonStudent
        foreach($students as $student) {
            $studentLesson = new LessonStudent();
            $studentLesson->setLessonId($entityLesson->getId());
            $studentLesson->setStudentUserId($student->getUser()->getId());
            $studentLesson->setAbsent(false);
            $studentLesson->setJustified(false);

            $em->persist($studentLesson);
        }
        // Insert all student
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
