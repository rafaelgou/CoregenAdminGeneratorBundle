<?php

namespace Coregen\AdminGeneratorBundle\ORM\Controller;

use Coregen\AdminGeneratorBundle\Controller;

class GeneratorController extends Controller\GeneratorController
{

    /**
     * Lists all Tarefa objects.
     *
     */
    public function indexAction()
    {
        $manager = $this->getDoctrine()->getEntityManager();

        $objects = $manager->getRepository('RgouGettyDoneBundle:Tarefa')->findAll();

        return $this->render('RgouGettyDoneBundle:Tarefa:index.html.twig', array(
            'objects' => $objects
        ));
    }

    /**
     * Finds and displays a Tarefa entity.
     *
     */
    public function showAction($id)
    {
        $manager = $this->getDoctrine()->getEntityManager();

        $entity = $manager->getRepository('RgouGettyDoneBundle:Tarefa')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tarefa entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('RgouGettyDoneBundle:Tarefa:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to create a new Tarefa entity.
     *
     */
    public function newAction()
    {
        $entity = new Tarefa();
        $form   = $this->createForm(new TarefaType(), $entity);

        return $this->render('RgouGettyDoneBundle:Tarefa:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new Tarefa entity.
     *
     */
    public function createAction()
    {
        $entity  = new Tarefa();
        $request = $this->getRequest();
        $form    = $this->createForm(new TarefaType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getEntityManager();
            $manager->persist($entity);
            $manager->flush();

            return $this->redirect($this->generateUrl('tarefa_show', array('id' => $entity->getId())));

        }

        return $this->render('RgouGettyDoneBundle:Tarefa:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Tarefa entity.
     *
     */
    public function editAction($id)
    {
        $manager = $this->getDoctrine()->getEntityManager();

        $entity = $manager->getRepository('RgouGettyDoneBundle:Tarefa')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tarefa entity.');
        }

        $editForm = $this->createForm(new TarefaType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('RgouGettyDoneBundle:Tarefa:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Tarefa entity.
     *
     */
    public function updateAction($id)
    {
        $manager = $this->getDoctrine()->getEntityManager();

        $entity = $manager->getRepository('RgouGettyDoneBundle:Tarefa')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tarefa entity.');
        }

        $editForm   = $this->createForm(new TarefaType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $manager->persist($entity);
            $manager->flush();

            return $this->redirect($this->generateUrl('tarefa_edit', array('id' => $id)));
        }

        return $this->render('RgouGettyDoneBundle:Tarefa:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Tarefa entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getEntityManager();
            $entity = $manager->getRepository('RgouGettyDoneBundle:Tarefa')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Tarefa entity.');
            }

            $manager->remove($entity);
            $manager->flush();
        }

        return $this->redirect($this->generateUrl('tarefa'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

}
