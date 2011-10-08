<?php

namespace Coregen\AdminGeneratorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('CoregenAdminGeneratorBundle:Default:index.html.twig', array('name' => $name));
    }
}
