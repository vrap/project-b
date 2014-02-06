<?php
/**
 * SpeakerController.php
 * @author Valentin
 * 04/02/14.
 */

namespace Project\AppBundle\Controller;


use Project\AppBundle\Entity\SpeakerLessonRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SpeakerController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('ProjectAppBundle:Speaker:index.html.twig');
    }

    /**
     * Display students list and save absents
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
} 