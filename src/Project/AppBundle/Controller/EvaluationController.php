<?php

namespace Project\AppBundle\Controller;

use Project\AppBundle\Entity\StudentEvaluation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Project\AppBundle\Entity\Evaluation;
use Project\AppBundle\Entity\Criterion;
use Symfony\Component\HttpFoundation\Request;
use Project\AppBundle\Form\EvaluationType;
use Project\AppBundle\Form\ValidateEvaluationType;
use Project\AppBundle\Entity\Speaker;
use Symfony\Component\Process\Exception\InvalidArgumentException;
/**
 * Criterion controller.
 *
 * @Route("/evaluation")
 */
class EvaluationController extends Controller
{

    /**
     * Lists all Evaluation entities.
     *
     * @Route("/", name="evaluation")
     * @Secure(roles={"ROLE_SPEAKER", "ROLE_MANAGER", "ROLE_STUDENT"})
     * @Method("GET")
     * @Template("ProjectAppBundle:Evaluation:index.html.twig")
     */
    public function indexAction()
    {
        // Get user logged in
        $user = $this->getUser();
        $userRoles = $user->getRoles();

        // init database
        $em = $this->getDoctrine()->getManager();
        $repoStudentEval = $em->getRepository('ProjectAppBundle:StudentEvaluation');
        $repoEval = $em->getRepository('ProjectAppBundle:Evaluation');
        $repoStudent = $em->getRepository('ProjectAppBundle:Student');
        $repoCrit = $em->getRepository('ProjectAppBundle:Criterion');
        $repoStudentEvalCrit = $em->getRepository('ProjectAppBundle:StudentEvaluationCriterion');
        
        // Si l'utilisateur est MANAGER ou SPEAKER
        if(in_array('ROLE_MANAGER', $userRoles)){
            $evals = $repoEval->findAllUnvalidatedByPromotion($this->get('session')->get('promotion'));

            if(!empty($evals)) {
                foreach($evals as $eval) {
                    $entities[] = array(
                            'evaluation' => $eval,
                            'criterions' => $repoCrit->findByEvaluation($eval)
                    );
                }
            } else {
                $entities = array();
            }


        } else if (in_array('ROLE_STUDENT', $userRoles)) {

            $student = $repoStudent->findOneByUser($user);
            $studentEvals = $repoStudentEval->findByStudent($student);
            $studentCritScores = array();

            foreach($studentEvals as $studentEval) {
                $criterions = $repoCrit->findByEvaluation($studentEval->getEvaluation());

                foreach ( $criterions as $criterion ) {
                    $critEval = $repoStudentEvalCrit->findOneByCritEval($criterion, $studentEval);

                    $studentCritScores[] = array(
                        'criterion' => $criterion,
                        'score' => $critEval->getScore()
                    );
                }

                $entities[] = array(
                    'evaluation' => $studentEval->getEvaluation(),
                    'criterions' => $studentCritScores,
                    'notation' => array(
                            'score' => $studentEval->getScore(),
                            'comment' => $studentEval->getComment()
                        )
                );
            }

        } else{

            $evals = $repoEval->findBySpeaker($user->getId());

            foreach($evals as $eval) {
                $entities[] = array(
                        'evaluation' => $eval,
                        'criterions' => $repoCrit->findByEvaluation($eval)
                );
            }
        }

        return array(
                'evaluationsList' => $entities,
        );


    }

    /**
     * Creates a new Evaluation entity.
     *
     * @Secure(roles="ROLE_SPEAKER")
     * @Route("/", name="evaluation_create")
     * @Method("POST")
     * @Template("ProjectAppBundle:Evaluation:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Evaluation();
        $form   = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $speaker = $em->getRepository('ProjectAppBundle:Speaker')->find($this->getUser()->getId());

            if(null === $speaker) {
                throw new InvalidArgumentException('Speaker is not defined');
            }

            $entity->setSpeaker($speaker);
            $em->persist($entity);

            $module = $entity->getModule();
            $promotion = $module->getPromotion();
            $students = $em->getRepository('ProjectAppBundle:Student')
                    ->findByPromotion($promotion);

            // Insertion in table student_evaluation
            foreach($students as $student) {
                $studentEval = new StudentEvaluation();
                $studentEval->setEvaluation($entity);
                $studentEval->setScore(0);
                $studentEval->setComment('Non évalué pour le moment.');
                $studentEval->setStudent($student);

                $em->persist($studentEval);
            }

            $em->flush();

            $this->get('session')->getFlashBag()->add('info', 'Évaluation enregistrée.');

            if('submit' == $form->getClickedButton()->getName()) {

                return $this->redirect($this->generateUrl('evaluation'));
            } else if('criterions_add' == $form->getClickedButton()->getName()) {

                return $this->redirect($this->generateUrl('criterion_new', array(
                        'id_eval' => $entity->getId()
                )));
            }
        }


        return array(
                'entity' => $entity,
                'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Evaluation entity.
     *
     * @param Evaluation $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Evaluation $entity)
    {
        $form = $this->createForm(new EvaluationType(), $entity, array(
                'action' => $this->generateUrl('project_app_evaluation_create'),
                'method' => 'POST'
        ));

        $form->add('submit', 'submit', array(
                'label' => 'Enregistrer',
                'attr' => (array( 'class' => 'btn btn-second' ))
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Evaluation entity.
     *
     * @Secure(roles="ROLE_SPEAKER")
     * @Route("/new", name="evaluation_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Evaluation();
        $form   = $this->createCreateForm($entity);

        return array(
                'entity' => $entity,
                'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Evaluation entity.
     *
     * @Secure(roles="ROLE_SPEAKER")
     * @Route("/{id}", name="evaluation_show")
     * @Method("GET")
     * @Template("ProjectAppBundle:Evaluation:show.html.twig")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProjectAppBundle:Evaluation')->find($id);
        $criterions = $em->getRepository('ProjectAppBundle:Criterion')->findAllByEvaluation($entity);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Evaluation entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
                'entity'      => $entity,
                'criterions'  => $criterions,
                'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Evaluation entity.
     *
     * @Secure(roles="ROLE_SPEAKER")
     * @Route("/{id}/edit", name="evaluation_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProjectAppBundle:Evaluation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Evaluation entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Evaluation entity.
     *
     * @param Evaluation $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Evaluation $entity)
    {
        $form = $this->createForm(new EvaluationType(), $entity, array(
                'action' => $this->generateUrl('evaluation_update', array('id' => $entity->getId())),
                'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Enregistrer les modifications',
            'attr' => (array( 'class' => 'btn btn-second' ))
            ));

        return $form;
    }
    /**
     * Edits an existing Evaluation entity.
     *
     * @Secure(roles="ROLE_SPEAKER")
     * @Route("/{id}", name="evaluation_update")
     * @Method("PUT")
     * @Template("ProjectAppBundle:Evaluation:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProjectAppBundle:Evaluation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Evaluation entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add('info', 'Évaluation enregistrée.');

            //return $this->redirect($this->generateUrl('evaluation_edit', array('id' => $id)));
            if('submit' == $editForm->getClickedButton()->getName()) {

                return $this->redirect($this->generateUrl('evaluation_edit', array(
                    'id' => $entity->getId()
                )));
            } else if('criterions_add' == $editForm->getClickedButton()->getName()) {

                return $this->redirect($this->generateUrl('criterion_new', array(
                        'id_eval' => $entity->getId()
                )));
            }
        }

        return array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Evaluation entity.
     *
     * @Secure(roles="ROLE_SPEAKER")
     * @Route("/{id}", name="evaluation_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ProjectAppBundle:Evaluation')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Evaluation entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('info', 'Évaluation supprimée.');

        }

        return $this->redirect($this->generateUrl('evaluation'));
    }

    /**
     * Creates a form to delete a Evaluation entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
                ->setAction($this->generateUrl('evaluation_delete', array('id' => $id)))
                ->setMethod('DELETE')
                ->add('submit', 'submit', array(
                    'label' => 'Supprimer',
                    'attr' => (array( 'class' => 'btn btn-default' ))
                    ))
                ->getForm()
                ;
    }

    /**
     * Show an Evaluation to validate.
     *
     * @Secure(roles="ROLE_MANAGER")
     * @Route("/{id}/validate", name="evaluation_validate")
     * @METHOD({"GET", "PUT"})
     * @Template("ProjectAppBundle:Evaluation:validate.html.twig")
     */
    public function validateForm(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $evaluation = $em->getRepository('ProjectAppBundle:Evaluation')->findOneBy(array(
                                                                                         'id' => $id,
                                                                                         'validated' => 0
        ));
        $promotion = $evaluation->getModule()->getPromotion();

        if ($this->get('session')->get('promotion') == $promotion->getId()) {
            $criterions = $em->getRepository('ProjectAppBundle:Criterion')->findBy(array(
                'evaluation' => $id
            ));

            $studentNotes = $em->getRepository('ProjectAppBundle:StudentEvaluation')->findBy(array(
                'evaluation' => $id
            ));

            $notes = array();
            foreach ($studentNotes as $note) {
                $notes[$note->getStudent()->getId()]['infos'] = $note;
                $studentCriterions = $em->getRepository('ProjectAppBundle:StudentEvaluationCriterion')->findBy(array(
                                                                                                                     'studentEvaluation' => $note->getId()
                                                                                                                     ));

                foreach ($studentCriterions as $criterion) {
                    $notes[$note->getStudent()->getId()]['details'][$criterion->getId()] = $criterion;
                }
            }

            $form = $this->createForm(new ValidateEvaluationType(), $evaluation, array(
                                                                                       'action' => $this->generateUrl('evaluation_validate', array('id' => $evaluation->getId())),
                                                                                       'method' => 'PUT'
                                                                                       ));

            $form->add('save', 'submit', array(
                                               'label' => 'Valider les notes',
                                               'attr' => array('class' => 'save btn btn-second pull-right'),
                                               ));

            $form->handleRequest($request);
            
            if ($form->isValid() && $form->get('save')->isClicked()) {
                $evaluation->setValidated(true);
                $em->flush();
                $this->get('session')->getFlashBag()->add('info', 'Évaluation enregistrée.');
                
                return $this->redirect($this->generateUrl('evaluation'));
            }

            return array(
                         'criterions' => $criterions,
                         'evaluation' => $evaluation,
                         'notes'      => $notes,
                         'form'       => $form->createView()
            );
        }
        else {
            $this->get('session')->getFlashBag()->add('error', 'Évaluation non existante.');
        }

        return $this->redirect($this->generateUrl('evaluation'));
    }
}
