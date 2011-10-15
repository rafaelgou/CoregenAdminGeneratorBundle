<?php

namespace Coregen\AdminGeneratorBundle\ORM;

use Coregen\AdminGeneratorBundle\Controller;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\DefaultCsrfProvider;

abstract class GeneratorController extends Controller\GeneratorController
{
    //abstract public function configure();

    public function indexAction()
    {
        // Configuring the Generator Controller
        $this->configure();

        // Defining actual page
        $this->setPage($this->getRequest()->get('page', $this->getPage()));

        $pager = $this->getPager();

//        $csrfProvider = new DefaultCsrfProvider($this->container->getParameter('kernel.secret'));

        $deleteForm = $this->createDeleteForm('fake');

        return $this->renderView('list' . ucfirst($this->generator->list->layout), array(
            'pager'      => $pager,
//            'csrf_token' => $csrfProvider->generateCsrfToken('delete_record'),
            'delete_form' => $deleteForm->createView(),
        ));

    }

    /**
     * Finds and displays a Tarefa entity.
     *
     */
    public function showAction($id)
    {
        // Configuring the Generator Controller
        $this->configure();

        $manager = $this->getDoctrine()->getEntityManager();

        $entity = $manager->getRepository($this->generator->model)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->renderView('show', array(
            'record'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to create a new Tarefa entity.
     *
     */
    public function newAction()
    {
        // Configuring the Generator Controller
        $this->configure();

        $entityClass = $this->generator->class;
        $formType = $this->generator->form->type;

        $entity = new $entityClass();
        $form   = $this->createForm(new $formType(), $entity);

        return $this->renderView('new', array(
            'record' => $entity,
            'new_form'   => $form->createView()
        ));
    }

    /**
     * Creates a new Tarefa entity.
     *
     */
    public function createAction()
    {
        // Configuring the Generator Controller
        $this->configure();

        $entityClass = $this->generator->class;
        $formType = $this->generator->form->type;

        $entity = new $entityClass();
        $form   = $this->createForm(new $formType(), $entity);

        $form->bindRequest($this->getRequest());

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getEntityManager();
            $manager->persist($entity);
            $manager->flush();

            $this->get('session')->setFlash('success', 'The item was created successfully');
            return $this->redirect($this->generateUrl($this->generator->route . '_show', array('id' => $entity->getId())));
        }

        $this->get('session')->setFlash('error', 'An error ocurred while saving the item. Checked data.');
        return $this->renderView('new', array(
            'record' => $entity,
            'new_form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Tarefa entity.
     *
     */
    public function editAction($id)
    {
        // Configuring the Generator Controller
        $this->configure();

        $manager = $this->getDoctrine()->getEntityManager();

        $entity = $manager->getRepository($this->generator->model)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Entity.');
        }

        $formType = $this->generator->form->type;

        $editForm = $this->createForm(new $formType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        foreach ($this->generator->getHiddenFields('edit') as $fieldName) {
            $editForm->remove($fieldName);
        }

        return $this->renderView('edit', array(
            'record'      => $entity,
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
        // Configuring the Generator Controller
        $this->configure();

        $manager = $this->getDoctrine()->getEntityManager();

        $entity = $manager->getRepository($this->generator->model)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find entity.');
        }

//        echo '<pre>'; print_r($entity); echo '</pre>';

        $formType = $this->generator->form->type;

        $editForm = $this->createForm(new $formType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        foreach ($this->generator->getHiddenFields('edit') as $fieldName) {
            $editForm->remove($fieldName);
        }
        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $manager->persist($entity);
            $manager->flush();

            $this->get('session')->setFlash('success', 'The item was updated successfully');
            return $this->redirect($this->generateUrl($this->generator->route . '_show', array('id' => $id)));
        } else {
            $this->get('session')->setFlash('error', 'An error ocurred while saving the item. Check the informed data.');
            return $this->renderView('edit', array(
                'record'      => $entity,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            ));

        }

    }

    /**
     * Deletes a Tarefa entity.
     *
     */
    public function deleteAction($id)
    {
        // Configuring the Generator Controller
        $this->configure();

        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getEntityManager();
            $entity = $manager->getRepository($this->generator->class)->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find entity.');
            }

            $manager->remove($entity);
            $manager->flush();

            $this->get('session')->setFlash('success', 'The item was deleted successfully');
            return $this->redirect($this->generateUrl($this->generator->route));
        } else {
            //$errors = array();
            //foreach($form->getErrors() as $error) {
            //    $errors[] = $error->getMessageTemplate();
            //}

            //echo '<pre>'; print_r($_REQUEST);echo implode('<br/>', $errors); print_r($form->getData());exit;

            $this->get('session')->setFlash(
                    'error',
                    'An error ocurred while deleting the item.'
                    //.'<br/>' . implode('<br/>', $errors)
                    );
            return $this->redirect($this->generateUrl($this->generator->route));
        }

    }

    protected function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

}
