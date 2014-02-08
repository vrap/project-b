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
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('speaker_evaluations'));
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
