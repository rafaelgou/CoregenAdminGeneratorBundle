<?php

namespace Coregen\AdminGeneratorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GeneratorController extends Controller
{
    protected $templates = array(
        'listGrid'    => 'CoregenAdminGeneratorBundle:Default:listGrid.html.twig',
        'listStacked' => 'CoregenAdminGeneratorBundle:Default:listStacked.html.twig',
        'edit'        => 'CoregenAdminGeneratorBundle:Default:edit.html.twig',
        'new'         => 'CoregenAdminGeneratorBundle:Default:new.html.twig',
        'form'        => 'CoregenAdminGeneratorBundle:Default:form.html.twig'
    );

    protected $objectClass = null;

    abstract public function indexAction();

    abstract public function showAction($id);

    abstract public function newAction();

    abstract public function createAction();

    abstract public function editAction($id);

    abstract public function updateAction($id);

    abstract public function deleteAction($id);

    abstract private function createDeleteForm($id);

}
