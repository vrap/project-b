<?php

namespace Project\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Project\AppBundle\Entity\Evaluation;
use Symfony\Component\HttpFoundation\Request;
use Project\AppBundle\Form\EvaluationType;
use Project\AppBundle\Entity\Speaker;
use Symfony\Component\Process\Exception\InvalidArgumentException;

class EvaluationController extends Controller
{

    public function indexAction()
    {
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

    private function createCreateForm(Evaluation $entity)
    {
        $form = $this->createForm(new EvaluationType(), $entity, array(
                'action' => $this->generateUrl('project_app_evaluation_create'),
                'method' => 'POST'
        ));

        $form->add('submit', 'submit', array(
                'label' => 'Create'
        ));

        return $form;
    }
}
