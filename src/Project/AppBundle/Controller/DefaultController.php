<?php

namespace Project\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;


class DefaultController extends Controller
{
    /**
     * Routes to the right application part in function of role.
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
            // Redirect to promotion gestion
            return $this->redirect($this->generateUrl('promotion'));
        } elseif(in_array('ROLE_SPEAKER', $userRoles)) {
            // Redirect to missing list
            return $this->redirect($this->generateUrl('missing'));
        } elseif(in_array('ROLE_STUDENT', $userRoles)) {
            // Redirect to the agenda
            return $this->redirect($this->generateUrl('agenda_index'));
        } elseif(in_array('ROLE_ADMIN', $userRoles)) {
            // Redirect to the agenda
            return $this->redirect($this->generateUrl('agenda_index'));
        } elseif(in_array('ROLE_SUPER_ADMIN', $userRoles)) {
            // Redirect to the agenda
            return $this->redirect($this->generateUrl('agenda_index'));
        }
    }
}
