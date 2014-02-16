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
     * @Secure(roles={"ROLE_SPEAKER", "ROLE_MANAGER"})
     * @Method("GET")
     * @Template("ProjectAppBundle:Evaluation:index.html.twig")
     */
    public function indexAction()
    {
        // Récupération de l'utilisateur connecté
        $user = $this->getUser();
        $userRoles = $user->getRoles();

        // Préparation de la base
        $em = $this->getDoctrine()->getManager();
        
        // Si l'utilisateur est MANAGER ou SPEAKER
        if(in_array('ROLE_MANAGER', $userRoles)){
            $entities = $em->getRepository('ProjectAppBundle:Evaluation')->findAll();
        }else{
            $entities = $em->getRepository('ProjectAppBundle:Evaluation')->findBySpeaker($user->getId());
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
                    ->findByPromotionId($promotion->getId());

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

            if('submit' == $form->getClickedButton()->getName()) {

                return $this->redirect($this->generateUrl('speaker_evaluations'));
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
                'label' => 'Enregistrer'
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

        $form->add('submit', 'submit', array('label' => 'Enregistrer les modifications'));

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
        }

        return $this->redirect($this->generateUrl('speaker_evaluations'));
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
                ->add('submit', 'submit', array('label' => 'Supprimer'))
                ->getForm()
                ;
    }
}
