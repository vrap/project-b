<?php

namespace Project\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Project\AppBundle\Entity\Evaluation;
use Project\AppBundle\Form\EvaluationType;

class EvaluationController extends Controller
{

    /**
     * Display all Evaluation
     */
    public function indexAction()
    {
        $em                  = $this->getDoctrine()->getManager();
        $repositoryManager   = $em->getRepository('ProjectAppBundle:Evaluation');
        $evaluationsList         = $repositoryManager->findAll();

        return $this->render('ProjectAppBundle:Evaluation:index.html.twig', array(
            'evaluationsList'   => $evaluationsList,
        ));
    }


    /**
     * Create a new eval when method post
     * Display form when method get
     */
    public function createAction()
    {
        $request = $this->get('request');
        $em  = $this->getDoctrine()->getManager();
        $evaluation = new Evaluation();
        $form = $this->createForm(new EvaluationType, $evaluation);

        if($request->isMethod('POST')) {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->persist($evaluation);
                $em->flush();

                return $this->redirect($this->generateUrl('project_app_user_index'));
            }
        } else {

            return $this->render('ProjectAppBundle:Evaluation:create.html.twig', array(
                'form' => $form->createView()
            ));
        }


    }
}
