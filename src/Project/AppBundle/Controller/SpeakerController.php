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
    public function indexAction()
    {
        return $this->render('ProjectAppBundle:Speaker:index.html.twig');
    }

    public function missingsAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()
                ->getManager();
        $repositorySpeaker = $em->getRepository('ProjectAppBundle:Speaker');
        $repositorySpeakerLesson = $em->getRepository('ProjectAppBundle:SpeakerLesson');
        $repositoryLesson = $em->getRepository('ProjectAppBundle:Lesson');
        $repositoryLessonStudent = $em->getRepository('ProjectAppBundle:LessonStudent');

        // Speaker's lessons
        $lessons = $repositorySpeakerLesson->findLessonsBySpeaker($user->getId());
        // Lesson of th day
        $todayLesson = $repositoryLesson->findTodayLessonId();
        if(in_array($todayLesson, $lessons)){
            // The speaker assumes the lesson of the day
            // Get the students
            $students = $repositoryLessonStudent->findStudentsByLesson($todayLesson);

            return $this->render('ProjectAppBundle:Speaker:missings.html.twig', array(
                    'studentsList' => $students
            ));
        }
        // Display all students in speaker's lesson
        // If method post, save missings
    }
} 