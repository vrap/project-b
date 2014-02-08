<?php
/**
 * SpeakerController.php
 * @author Valentin
 * 04/02/14.
 */

namespace Project\AppBundle\Controller;

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
use Project\AppBundle\Form\EvaluationType;


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
     * @Route("/", name="speaker_missings")
     * @Method("POST")
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
    }
} 