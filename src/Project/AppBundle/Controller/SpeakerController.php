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
     * Marks students for an evaluation
     *
     * @Secure(roles="ROLE_SPEAKER")
     * @Route("/evaluate/{eval_id}", name="speaker_evaluate")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @param $eval_id
     * @throws \InvalidArgumentException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function evaluateAction($eval_id, Request $request) {
        $em = $this->getDoctrine()->getManager();

        $repo_eval = $em->getRepository('ProjectAppBundle:Evaluation');
        $repo_crit = $em->getRepository('ProjectAppBundle:Criterion');
        $repo_student_eval = $em->getRepository('ProjectAppBundle:StudentEvaluation');

        $evaluation = $repo_eval->find($eval_id);

        if(null === $evaluation) {
            throw new \InvalidArgumentException('Unable to find evaluation ' . $eval_id);
        }

        $criterions = $repo_crit->findAllByEvaluation($evaluation);
        $students = $repo_student_eval->findStudentsByEvaluation($evaluation);

        if('POST' === $request->getMethod())
        {
            $score_eval = $request->request->get('evaluation_score');
            $comment_eval = $request->request->get('evaluation_comment');

            $current_student = $em->getRepository('ProjectAppBundle:Student')
                    ->findOneByUserId($students[0]->getId());
            $current_student_eval = $repo_student_eval->findOneByEvalStudent($evaluation, $current_student);

            $current_student_eval->setScore($score_eval);
            $current_student_eval->setComment($comment_eval);

            $em->persist($current_student_eval);
            $em->flush();
        }

        return $this->render('ProjectAppBundle:Speaker:evaluate.html.twig', array(
            'evaluation' => $evaluation,
            'criterions' => $criterions,
            'students' => $students
        ));
    }
}