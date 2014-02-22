<?php

namespace Project\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Null;

/**
 * @Route("/missing")
 */
class MissingController extends Controller
{
    /**
     * Lists all missings for a manger.
     * Display students list for a speaker.
     * Show number of missings for a student.
     *
     * @Route("/", name="missing")
     * @Secure(roles={"ROLE_SPEAKER", "ROLE_MANAGER", "ROLE_STUDENT"})
     * @Method("GET")
     * @Template("ProjectAppBundle:Missing:index.html.twig")
     */
    public function indexAction()
    {
        // Get logged in user
        $user = $this->getUser();
        $userRoles = $user->getRoles();

        // Init database manager
        $em = $this->getDoctrine()->getManager();
        $repositoryLesson = $em->getRepository('ProjectAppBundle:Lesson');
        $repositoryLessonStudent = $em->getRepository('ProjectAppBundle:LessonStudent');
        $repositoryUser = $em->getRepository('ProjectAppBundle:User');

        if(in_array('ROLE_MANAGER', $userRoles)){
            // If a manager is connected
            $entities = $em->getRepository('ProjectAppBundle:Evaluation')->findAll();

        } elseif (in_array('ROLE_SPEAKER', $userRoles)) {
            // If a speaker is connected
            // Lesson of the day
            $todayLesson = $repositoryLesson->findTodayLesson();
            if(null === $todayLesson) {
                $this->get('session')->getFlashBag()
                        ->add('info', 'Il n\'y a pas de cours aujourd\'hui.');

                return array(
                        'studentsList' => null
                );
            }

            // The speaker assumes the lesson of the day
            if($todayLesson->getSpeaker()->getId() == $user->getId()){
                // Get the students
                $students = array();
                $dataStudents = $repositoryLessonStudent->findStudentsByLessonId($todayLesson->getId());
                foreach($dataStudents as $val) {
                    $students[] = $repositoryUser->findUserById($val['studentUserId']);
                }
                // No students had participated at this lesson
                if(empty($students)) {
                    $this->get('session')->getFlashBag()
                            ->add('error', 'Une erreur est survenue lors de la création du cours.');

                    return array(
                        'studentsList' => null
                    );
                }

                // Display all students in speaker's lesson
                return array(
                        'studentsList' => $students
                );
            }

            // Speaker is not in charge of the lesson today
            $this->get('session')->getFlashBag()
                    ->add('info', 'Vous n\'avez pas de cours aujourd\'hui.');

            return array(
                    'studentsList' => null
            );

        } elseif (in_array('ROLE_STUDENT', $userRoles)) {
            // If a student is connected

        } else {
            $this->get('session')->getFlashBag()
                    ->add('error', 'Votre statut ne vous permet pas d\'accéder à cette partie.');

            return array(
                    'studentsList' => null
            );
        }

    }

    /**
     * Save missings.
     *
     * @Route("/save", name="missing_save")
     * @Secure(roles={"ROLE_SPEAKER"})
     * @Method("POST")
     * @Template("ProjectAppBundle:Missing:index.html.twig")
     */
    public function saveAction(Request $request) {
        // Init database manager
        $em = $this->getDoctrine()->getManager();
        $repositoryLessonStudent = $em->getRepository('ProjectAppBundle:LessonStudent');
        $repositoryLesson = $em->getRepository('ProjectAppBundle:Lesson');

        $todayLesson = $repositoryLesson->findTodayLesson();

        // Get missings
        $studentsMissing = $request->request->get('missings');

        // Save missings
        if(null !== $studentsMissing)
        {
            foreach ($studentsMissing as $absentId) {
                $res = $repositoryLessonStudent->setAbsent($absentId, $todayLesson->getId());

                // An error has occured during saving.
                if(false === $res)
                {
                    $this->get('session')->getFlashBag()
                            ->add('error', 'Une erreur inconnue est survenue. Veuillez nous excuser.');

                    return array(
                            'studentsList' => null
                    );
                }
            }

            // All absents are saved
            $this->get('session')->getFlashBag()
                    ->add('success', 'L\'appel est enregistré. Les absences seront transmises au(x) responsable(s) de la formation.');

            return array(
                    'studentsList' => null
            );
        }

        // Nobody is absent
        $this->get('session')->getFlashBag()
                ->add('success', 'L\'appel est enregistré. Aucun absent.');

        return array(
                'studentsList' => null
        );
    }
}
