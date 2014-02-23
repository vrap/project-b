<?php

namespace Project\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Project\AppBundle\Entity\Manager;
use Project\AppBundle\Form\ManagerType;

/**
 * Manager controller.
 *
 * @Route("/manager")
 */
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

    /**
     * Creates a new Manager entity.
     *
     * @Route("/", name="manager_create")
     * @Method("POST")
     * @Template("ProjectAppBundle:User:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Manager();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $entity->getUser()->setEnabled(1);
            $entity->getUser()->setRoles(array('ROLE_MANAGER'));
            $entity->setIsAdministrator(0);
            $this->get('session')->getFlashBag()->add('info', 'L\'utilisateur à bien été ajouté');
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user'));
        }

        return array(
            'entity' => $entity,
            'title'  => 'Créer un Responsable',
            'form'   => $form->createView(),
        );

    }

    /**
    * Creates a form to create a Manager entity.
    *
    * @param Student $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Manager $entity)
    {
        $form = $this->createForm(new ManagerType(), $entity, array(
            'action' => $this->generateUrl('manager_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Ajouter', 'attr' => array('class' => 'btn btn-second')));

        return $form;
    }

    /**
     * Displays a form to create a new Manager entity.
     *
     * @Route("/new", name="user_manager_new")
     * @Method("GET")
     * @Template("ProjectAppBundle:Manager:new.html.twig")
     */
    public function newAction()
    {
        $entity = new Manager();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'title'  => 'Créer un Responsable',
            'form'   => $form->createView(),
        );
    }

    /**
     * Enables Manager to export Student absences.
     *
     * @Route("/export_absence", name="manager_export_absence")
     */
    public function exportAbsenceAction()
    {
        $em         = $this->getDoctrine()->getManager();
        $module     = $em->getRepository('ProjectAppBundle:Module')->find(1);
        $moduleName = $module->getName();
        $lessons    = $em->getRepository('ProjectAppBundle:Lesson')->findBy(array(
            'module' => $module->getId()
        ));
        $handle     = fopen('php://memory', 'r+');
        $header     = array();

        if (! $module) {
            throw $this->createNotFoundException('Impossible de trouver le module.');
        }

        if (! $lessons) {
            throw $this->createNotFoundException('Impossible de trouver les cours.');
        }

        foreach ($lessons as $lesson) {
            $lessonsStudents = $em->getRepository('ProjectAppBundle:LessonStudent')->findStudentsByLesson($lesson->getId());

            foreach ($lessonsStudents as $lessonStudent) { 
                $student       = $em->getRepository('ProjectAppBundle:Student')->find($lessonStudent['studentUserId']);
                $user          = $em->getRepository('ProjectAppBundle:User')->find($student->getUser());
                $lessonStudent = $em->getRepository('ProjectAppBundle:LessonStudent')->findBy(array(
                    'lessonId'      => $lesson->getId(),
                    'studentUserId' => $lessonStudent['studentUserId'],
                ));

                $dateLesson = $lesson->getStartDate();
                $idStudent  = $user->getId();
                $absence    = (($lessonStudent[0]->getAbsent())) ? 1 : 0;

                if ($dateLesson && $idStudent && $absence) {
                    fputcsv($handle, array($moduleName, $dateLesson->format('d-m-Y'), $dateLesson->format('H:i'), $idStudent, $absence));
                }
            }
        }

        rewind($handle);
        
        $content = stream_get_contents($handle);
        
        fclose($handle);

        return new Response($content, 200, array(
            'Content-Type'        => 'application/force-download',
            'Content-Disposition' => 'attachment; filename="export_' . rawurlencode($moduleName) . '_absences.csv"',
        ));
    }

    /**
     * Enables Manager to export Student scores.
     *
     * @Route("/export_score", name="manager_export_score")
     */
    public function exportScoreAction()
    {
        $em          = $this->getDoctrine()->getEntityManager();
        $module      = $em->getRepository('ProjectAppBundle:Module')->find(1);
        $evaluations = $em->getRepository('ProjectAppBundle:Evaluation')->findBy(array(
            'module' => $module->getId()
        ));
        $handle      = fopen('php://memory', 'r+');
        $header      = array();

        foreach ($evaluations as $evaluation) {
            $studentsEvaluations = $em->getRepository('ProjectAppBundle:StudentEvaluation')->findBy(array(
                'evaluation' => $evaluation->getId()
            ));

            foreach ($studentsEvaluations as $studentEvaluation) {
                $student = $em->getRepository('ProjectAppBundle:Student')->find($studentEvaluation->getId());

                error_log(print_r($student, TRUE));
                exit(0);

                fputcsv($handle, array(1, $student->getId(), $studentEvaluation->getScore()));
            }
        }

        rewind($handle);
        
        $content = stream_get_contents($handle);
        
        fclose($handle);

        $moduleName = $module->getName();

        return new Response($content, 200, array(
            'Content-Type'        => 'application/force-download',
            'Content-Disposition' => 'attachment; filename="export_' . rawurlencode($moduleName) . '_absences.csv"',
        ));
    }
}