<?php

namespace Project\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Controller\SecurityController as BaseController;

class SecurityController extends BaseController
{
    protected function renderLogin(array $data)
    {
        $template = sprintf('ProjectAppBundle:User:login.html.twig', $this->container->getParameter('fos_user.template.engine'));

        return $this->container->get('templating')->renderResponse($template, $data);        
    }
}