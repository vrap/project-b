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
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("/speaker")
 */
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
        // Get logged in user
        $user = $this->getUser();

        if(null === $user) {
            return $this->render('ProjectAppBundle:Speaker:missings.html.twig', array(
                    'msg' => 'Vous devez être connecté au préalable.'
            ));
        }

        // Init repositories
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
            // Get missings
            $studentsMissing = $request->request->get('missings');

            // Save missings
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

        // The speaker assumes the lesson of the day
        if(in_array($lessonId, $lessons)){
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
     * Marks students for an evaluation
     *
     * @Secure(roles="ROLE_SPEAKER")
     * @Route("/evaluate/{eval_id}", name="speaker_evaluate")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @param $eval_id
     * @param Request $request
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function evaluationsAction($eval_id, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $session = new Session();

        // Set current student
        if(false === $session->has('cpt_student')) {
            $session->set('cpt_student', 0);
            $cpt_student = $session->get('cpt_student');
        } else {
            $cpt_student = $session->get('cpt_student');
        }

        // Repositories
        $repo_eval = $em->getRepository('ProjectAppBundle:Evaluation');
        $repo_crit = $em->getRepository('ProjectAppBundle:Criterion');
        $repo_student_eval = $em->getRepository('ProjectAppBundle:StudentEvaluation');
        $repo_student_eval_crit = $em->getRepository('ProjectAppBundle:StudentEvaluationCriterion');

        // Find current evaluation
        $evaluation = $repo_eval->find($eval_id);

        if(null === $evaluation) {
            throw new \InvalidArgumentException('Unable to find evaluation ' . $eval_id);
        }

        // Find corresponding criterions
        $criterions = $repo_crit->findAllByEvaluation($evaluation);
        // Find students who had participated
        $students = $repo_student_eval->findStudentsByEvaluation($evaluation);

        if(empty($students)) {
            throw new \Exception('Array $students can not be empty');
        }

        // Actions when submiting
        if('POST' === $request->getMethod())
        {
            // Values given by the speaker
            $score_eval = $request->request->get('evaluation_score');

            // Score verifications
            if("" == $score_eval) {
                $this->get('session')->getFlashBag()->add('error', 'Veuillez renseigner la note de l\'évaluation.');

                return $this->render('ProjectAppBundle:Speaker:evaluate.html.twig', array(
                        'evaluation' => $evaluation,
                        'criterions' => $criterions,
                        'student' => $students[$cpt_student],
                ));
            }

            if(is_nan($score_eval) || $score_eval > $evaluation->getMax()) {
                $this->get('session')->getFlashBag()->add('error', 'La note de l\'évaluation n\'est pas valide.');

                return $this->render('ProjectAppBundle:Speaker:evaluate.html.twig', array(
                        'evaluation' => $evaluation,
                        'criterions' => $criterions,
                        'student' => $students[$cpt_student],
                ));
            }

            // Get the commentary.
            // It's not required so there is no verifications
            $comment_eval = $request->request->get('evaluation_comment');

            // Find students marked
            $current_student = $em->getRepository('ProjectAppBundle:Student')
                    ->findOneByUser($students[$cpt_student]);

            // Hydrate StudentEvaluation table
            $current_student_eval = $repo_student_eval->findOneByEvalStudent($evaluation, $current_student);
            $current_student_eval->setScore($score_eval);
            $current_student_eval->setComment($comment_eval);
            $em->persist($current_student_eval);

            // Hydrate StudentEvaluationCriterion table if criterions are present
            $scores_crit = $request->request->get('criterion_score');
            if(null !== $scores_crit) {
                $cpt = 0;
                foreach($scores_crit as $score) {
                    // Score verifications
                    if("" == $score) {
                        $this->get('session')->getFlashBag()->add('error', 'Veuillez renseigner le détail du barème.');

                        return $this->render('ProjectAppBundle:Speaker:evaluate.html.twig', array(
                                'evaluation' => $evaluation,
                                'criterions' => $criterions,
                                'student' => $students[$cpt_student],
                        ));
                    }

                    if($score > $criterions[$cpt]->getMax() || is_nan($score)) {
                        $this->get('session')->getFlashBag()->add('error', 'Valeur invalide dans le détail du barème.');

                        return $this->render('ProjectAppBundle:Speaker:evaluate.html.twig', array(
                                'evaluation' => $evaluation,
                                'criterions' => $criterions,
                                'student' => $students[$cpt_student],
                        ));
                    }

                    // Save score
                    $current_student_eval_crit = $repo_student_eval_crit->findOneByCritEval($criterions[$cpt], $current_student_eval);
                    $current_student_eval_crit->setScore($score);
                    $em->persist($current_student_eval_crit);
                    $cpt++;
                }
            }

            $em->flush();

            $session->getFlashBag()->add('info', 'Notes enregistrées.');

            // Icrement current student
            $cpt_student++;
            if($cpt_student >= count($students)) {
                // Every students had been marked
                $session->remove('cpt_student');
                $session->getFlashBag()->add('info', 'L\'évaluation a bien été notée.
                    Les notes seront transimises au(x) responsable(s) de la formation.');

                return $this->redirect($this->generateUrl('evaluation'));
            }

            // Continue with the next student
            $session->set('cpt_student',$cpt_student);

            return $this->render('ProjectAppBundle:Speaker:evaluate.html.twig', array(
                    'evaluation' => $evaluation,
                    'criterions' => $criterions,
                    'student' => $students[$cpt_student],
            ));
        }

        return $this->render('ProjectAppBundle:Speaker:evaluate.html.twig', array(
            'evaluation' => $evaluation,
            'criterions' => $criterions,
            'student' => $students[$cpt_student],
        ));
    }
    
    
    /**
     * @Route("/add", name="speaker_add_lesson")
     * @Template()
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction($data)
    {
        $data = json_decode($data);
        $startDate = new \DateTime($data->startDate);
        $endDate = new \DateTime($data->endDate);

        $entity = new Lesson();
        $em = $this->getDoctrine()->getManager();

        $entity->setName($data->name);
        $entity->setStartDate($startDate);
        $entity->setEndDate($endDate);
        $entity->setTimecard(1);

        $em->persist($entity);
        $em->flush();
        
        return \Symfony\Component\HttpFoundation\Response::HTTP_ACCEPTED;
    }
}