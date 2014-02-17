<?php
/**
 * SpeakerController.php
 * @author Valentin
 * 04/02/14.
 */

namespace Project\AppBundle\Controller;

use Project\AppBundle\Entity\Evaluation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Project\AppBundle\Entity\SpeakerLesson;
use Project\AppBundle\Entity\Lesson;
use Project\AppBundle\Entity\LessonStudent;
use Project\AppBundle\Entity\User;
use Project\AppBundle\Entity\Speaker;
use Project\AppBundle\Form\EvaluationType;
use Project\AppBundle\Form\SpeakerType;


class SpeakerController extends Controller
{
    /**
     * @Secure(roles="ROLE_SPEAKER")
     * @Route("/", name="speaker")
     * @Method("GET")
     * @Template()
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('ProjectAppBundle:Speaker:index.html.twig');
    }

    /**
     * Display students list and save absents
     *
     * @Secure(roles="ROLE_SPEAKER")
     * @Route("/missings", name="speaker_missings")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function missingsAction(Request $request)
    {
        $user = $this->getUser();

        if(null === $user) {
            return $this->render('ProjectAppBundle:Speaker:missings.html.twig', array(
                    'msg' => 'Vous devez être connecté au préalable.'
            ));
        }

        $em = $this->getDoctrine()
                ->getManager();
        $repositorySpeakerLesson = $em->getRepository('ProjectAppBundle:SpeakerLesson');
        $repositoryLesson = $em->getRepository('ProjectAppBundle:Lesson');
        $repositoryLessonStudent = $em->getRepository('ProjectAppBundle:LessonStudent');
        $repositoryUser = $em->getRepository('ProjectAppBundle:User');

        // Speaker's lessons
        $lessons = array();
        $lessonsTemp = $repositorySpeakerLesson->findLessonsBySpeaker($user->getId());
        foreach ($lessonsTemp as $value) {
            $lessons[] = $value['lessonId'];
        }

        // Lesson of the day
        $todayLesson = $repositoryLesson->findTodayLessonId();
        if(empty($todayLesson)) {

            return $this->render('ProjectAppBundle:Speaker:missings.html.twig', array(
                    'msg' => 'Vous n\'avez pas de cours aujourd\'hui.'
            ));
        }
        $lessonId = $todayLesson[0]['id'];
        // If method post, save missings
        if('POST' === $request->getMethod())
        {
            $studentsMissing = $request->request->get('missings');

            if(null !== $studentsMissing)
            {
                foreach ($studentsMissing as $absentId) {
                    $res = $repositoryLessonStudent->setAbsent($absentId, $lessonId);
                    if(false === $res)
                    {
                        return $this->render('ProjectAppBundle:Speaker:missings.html.twig', array(
                                'msg' => 'Une erreur est survenue.'
                        ));
                    }
                }

                return $this->render('ProjectAppBundle:Speaker:missings.html.twig', array(
                        'msg' => 'L\'appel est enregistré. Les absences seront transmises au(x) responsable(s) de la formation.'
                ));
            }

            return $this->render('ProjectAppBundle:Speaker:missings.html.twig', array(
                    'msg' => 'L\'appel est enregistré. Aucun absent.'
            ));
        }

        if(in_array($lessonId, $lessons)){
            // The speaker assumes the lesson of the day
            // Get the students
            $students = array();
            $dataStudents = $repositoryLessonStudent->findStudentsByLesson($todayLesson);
            foreach($dataStudents as $val) {
                $students[] = $repositoryUser->findUserById($val['studentUserId']);
            }

            // Display all students in speaker's lesson
            return $this->render('ProjectAppBundle:Speaker:missings.html.twig', array(
                    'studentsList' => $students
            ));
        }

        return $this->render('ProjectAppBundle:Speaker:missings.html.twig', array(
                'msg' => 'Vous n\'avez pas de cours aujourd\'hui.'
        ));
    }

    /**
     * Display speaker's evaluations
     *
     * @Secure(roles="ROLE_SPEAKER")
     * @Route("/evaluations", name="speaker_evaluations")
     * @Method("GET")
     * @Template()
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function evaluationsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $evaluations = $em->getRepository('ProjectAppBundle:Evaluation')
                ->findAllBySpeaker($this->getUser()->getId());
        return $this->render('ProjectAppBundle:Speaker:evaluations.html.twig', array(
            'evaluationsList' => $evaluations
        ));
    }

    /**
     * Creates a new Speaker entity.
     *
     * @Route("/", name="speaker_create")
     * @Method("POST")
     * @Template("ProjectAppBundle:Speaker:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Speaker();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $entity->getUser()->setEnabled(1);
            $entity->getUser()->setRoles(array('ROLE_SPEAKER'));

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );

    }

    /**
    * Creates a form to create a Speaker entity.
    *
    * @param Student $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Speaker $entity)
    {
        $form = $this->createForm(new SpeakerType(), $entity, array(
            'action' => $this->generateUrl('speaker_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Ajouter', 'attr' => array('class' => 'btn btn-second')));

        return $form;
    }

    /**
     * Displays a form to create a new Speaker entity.
     *
     * @Route("/new", name="user_speaker_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Speaker();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }



}