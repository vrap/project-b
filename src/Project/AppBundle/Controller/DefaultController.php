<?php

namespace Project\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Project\AppBundle\Entity\User;
use Project\AppBundle\Form\UserType;

class DefaultController extends Controller
{
    /**
     * Lists all User entities.
     *
     * @Secure(roles={"ROLE_MANAGER", "ROLE_SPEAKER", "ROLE_STUDENT"})
     * @Route("/", name="project_app_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        // Get logged in user
        $user = $this->getUser();
        $userRoles = $user->getRoles();

        if(in_array('ROLE_MANAGER', $userRoles)) {
            return $this->redirect($this->generateUrl('promotion'));
        } elseif(in_array('ROLE_SPEAKER', $userRoles)) {
            return $this->redirect($this->generateUrl('missing'));
        } elseif(in_array('ROLE_STUDENT', $userRoles)) {
            return $this->redirect($this->generateUrl('project_app_agenda_index'));
        }
    }
}
